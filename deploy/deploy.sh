#!/usr/bin/env bash
#
# Zero-downtime deploy. Run on the server as the `deploy` user:
#     bash deploy.sh [branch]
#
# Layout:
#   /var/www/sitedesk/
#     releases/20261119-101500/   one folder per deploy
#     shared/.env                 kept between releases
#     shared/storage/             uploads, logs, sessions
#     current -> releases/...     the live release (Nginx root)
#
# Each release is fully built before `current` is switched, so visitors
# never see a half-deployed site. Rollback = point `current` back.
set -euo pipefail

BRANCH="${1:-main}"
REPO="${REPO:-https://github.com/HeinLattAung/sitedesk.git}"
APP_DIR=/var/www/sitedesk
KEEP=5
PHP_FPM=php8.4-fpm

RELEASE="$APP_DIR/releases/$(date +%Y%m%d-%H%M%S)"
log() { printf '\033[1;34m--> %s\033[0m\n' "$*"; }

if [ ! -f "$APP_DIR/shared/.env" ]; then
    echo "Missing $APP_DIR/shared/.env — create it first (see DEPLOYMENT.md)." >&2
    exit 1
fi

# If anything below fails, delete the half-built release; live site is untouched.
trap 'echo "Deploy failed — live site unchanged." >&2; rm -rf "$RELEASE"' ERR

log "Cloning $BRANCH into $RELEASE"
git clone --depth 1 --branch "$BRANCH" "$REPO" "$RELEASE"
cd "$RELEASE"

log "Linking shared .env and storage"
rm -rf storage
ln -s "$APP_DIR/shared/storage" storage
ln -s "$APP_DIR/shared/.env" .env

log "Composer install (no dev)"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

log "Building frontend assets"
npm ci --no-audit --no-fund
npm run build
rm -rf node_modules

log "Migrating database"
php artisan migrate --force

log "Caching config, routes, views, events"
php artisan storage:link
php artisan optimize

log "Switching current -> $(basename "$RELEASE")"
ln -sfn "$RELEASE" "$APP_DIR/current.tmp"
mv -Tf "$APP_DIR/current.tmp" "$APP_DIR/current"   # atomic swap
trap - ERR

log "Reloading PHP-FPM (clears OPcache) and restarting queue worker"
sudo systemctl reload "$PHP_FPM"
sudo supervisorctl restart 'sitedesk-worker:*' || true

log "Removing old releases (keeping $KEEP)"
ls -1dt "$APP_DIR"/releases/*/ | tail -n +$((KEEP + 1)) | xargs -r rm -rf

log "Health check"
if curl -fsS -o /dev/null -H "Host: $(grep -E '^APP_URL=' "$APP_DIR/shared/.env" | sed -E 's#.*//([^/]+).*#\1#')" http://127.0.0.1/up; then
    log "Deployed $(git rev-parse --short HEAD) successfully"
else
    echo "Health check failed! Roll back with: bash $APP_DIR/current/deploy/rollback.sh" >&2
    exit 1
fi
