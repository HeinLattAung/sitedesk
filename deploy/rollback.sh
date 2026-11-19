#!/usr/bin/env bash
#
# Point `current` back to the previous release. Run as `deploy`.
# Note: database migrations are NOT reversed automatically.
set -euo pipefail

APP_DIR=/var/www/sitedesk
CURRENT="$(readlink -f "$APP_DIR/current")"
PREVIOUS="$(ls -1dt "$APP_DIR"/releases/*/ | sed 's#/$##' | grep -vx "$CURRENT" | head -n 1)"

if [ -z "$PREVIOUS" ]; then
    echo "No previous release to roll back to." >&2
    exit 1
fi

echo "Rolling back: $(basename "$CURRENT") -> $(basename "$PREVIOUS")"
ln -sfn "$PREVIOUS" "$APP_DIR/current.tmp"
mv -Tf "$APP_DIR/current.tmp" "$APP_DIR/current"
sudo systemctl reload php8.4-fpm
sudo supervisorctl restart 'sitedesk-worker:*' || true
# Move the bad release aside so the next rollback doesn't pick it again.
mv "$CURRENT" "$CURRENT.failed"
echo "Done."
