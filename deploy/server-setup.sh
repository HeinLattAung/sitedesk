#!/usr/bin/env bash
#
# One-time provisioning for a fresh Ubuntu 24.04 VPS.
# Run as root:   sudo DOMAIN=example.com bash server-setup.sh
#
# Installs Nginx, PHP 8.4-FPM, MySQL 8, Node 22, Composer, Supervisor,
# Certbot, UFW and fail2ban; creates a `deploy` user, the app database and
# the release folder layout used by deploy.sh.
set -euo pipefail

DOMAIN="${DOMAIN:?Set DOMAIN, e.g. DOMAIN=example.com}"
APP=sitedesk
APP_DIR=/var/www/$APP
DEPLOY_USER=deploy
PHP=8.4
DB_NAME=$APP
DB_USER=$APP
DB_PASS="$(openssl rand -base64 24 | tr -d '/+=')"

log() { printf '\n\033[1;32m==> %s\033[0m\n' "$*"; }

log "Swap file (lets Composer/npm builds run on 1 GB RAM servers)"
if ! swapon --show | grep -q .; then
    fallocate -l 2G /swapfile && chmod 600 /swapfile && mkswap /swapfile && swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

log "System update + base packages"
export DEBIAN_FRONTEND=noninteractive
apt-get update -y && apt-get upgrade -y
apt-get install -y software-properties-common curl git unzip acl ufw fail2ban \
    nginx mysql-server supervisor certbot python3-certbot-nginx unattended-upgrades

log "PHP $PHP (ondrej/php PPA)"
add-apt-repository -y ppa:ondrej/php
apt-get update -y
apt-get install -y php$PHP-fpm php$PHP-cli php$PHP-mysql php$PHP-mbstring php$PHP-xml \
    php$PHP-curl php$PHP-zip php$PHP-bcmath php$PHP-intl php$PHP-gd php$PHP-opcache

# Production PHP tweaks: bigger uploads, no version header, OPcache on.
cat > /etc/php/$PHP/fpm/conf.d/99-$APP.ini <<INI
upload_max_filesize = 8M
post_max_size = 10M
memory_limit = 256M
expose_php = Off
opcache.enable = 1
opcache.memory_consumption = 128
opcache.validate_timestamps = 0
INI

log "Composer + Node 22"
curl -fsSL https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
curl -fsSL https://deb.nodesource.com/setup_22.x | bash -
apt-get install -y nodejs

log "Deploy user ($DEPLOY_USER)"
if ! id "$DEPLOY_USER" &>/dev/null; then
    adduser --disabled-password --gecos "" "$DEPLOY_USER"
    usermod -aG www-data "$DEPLOY_USER"
    mkdir -p /home/$DEPLOY_USER/.ssh
    # Reuse the admin's authorized key so you can `ssh deploy@server` right away.
    # (root on most VPSs; the `ubuntu` user on Oracle Cloud / AWS, via sudo.)
    KEY_SRC=/root/.ssh/authorized_keys
    if [ -n "${SUDO_USER:-}" ] && [ -s "/home/$SUDO_USER/.ssh/authorized_keys" ]; then
        KEY_SRC="/home/$SUDO_USER/.ssh/authorized_keys"
    fi
    cp "$KEY_SRC" /home/$DEPLOY_USER/.ssh/authorized_keys 2>/dev/null || true
    chown -R $DEPLOY_USER:$DEPLOY_USER /home/$DEPLOY_USER/.ssh
    chmod 700 /home/$DEPLOY_USER/.ssh
fi
# deploy may reload PHP-FPM and restart the queue worker, nothing else.
cat > /etc/sudoers.d/$DEPLOY_USER <<SUDO
$DEPLOY_USER ALL=(root) NOPASSWD: /usr/bin/systemctl reload php$PHP-fpm, /usr/bin/supervisorctl restart $APP-worker\:*
SUDO
chmod 440 /etc/sudoers.d/$DEPLOY_USER

log "MySQL database + user"
mysql <<SQL
CREATE DATABASE IF NOT EXISTS \`$DB_NAME\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON \`$DB_NAME\`.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
SQL
# Credentials for backup.sh (readable only by deploy).
cat > /home/$DEPLOY_USER/.my.cnf <<CNF
[client]
user=$DB_USER
password=$DB_PASS
CNF
chown $DEPLOY_USER:$DEPLOY_USER /home/$DEPLOY_USER/.my.cnf
chmod 600 /home/$DEPLOY_USER/.my.cnf

log "App folders: $APP_DIR/{releases,shared}"
mkdir -p $APP_DIR/releases $APP_DIR/shared/storage/{app/public,framework/{cache/data,sessions,views},logs}
chown -R $DEPLOY_USER:www-data $APP_DIR
chmod -R 2775 $APP_DIR/shared/storage
# Make sure PHP (www-data) can always write to storage, whoever created the file.
setfacl -R -m u:www-data:rwX -m d:u:www-data:rwX -m u:$DEPLOY_USER:rwX -m d:u:$DEPLOY_USER:rwX $APP_DIR/shared/storage

log "Nginx site"
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
sed "s/example.com/$DOMAIN/g" "$SCRIPT_DIR/nginx/sitedesk.conf" > /etc/nginx/sites-available/$APP
ln -sf /etc/nginx/sites-available/$APP /etc/nginx/sites-enabled/$APP
rm -f /etc/nginx/sites-enabled/default
nginx -t && systemctl reload nginx

log "Queue worker (Supervisor) + scheduler (cron)"
cp "$SCRIPT_DIR/supervisor/sitedesk-worker.conf" /etc/supervisor/conf.d/$APP-worker.conf
supervisorctl reread && supervisorctl update || true   # starts after first deploy
cat > /etc/cron.d/$APP <<CRON
* * * * * $DEPLOY_USER cd $APP_DIR/current && php artisan schedule:run >> /dev/null 2>&1
30 2 * * * $DEPLOY_USER bash $APP_DIR/current/deploy/backup.sh >> $APP_DIR/shared/storage/logs/backup.log 2>&1
CRON

log "Firewall + SSH hardening"
if grep -qs 'REJECT' /etc/iptables/rules.v4; then
    # Oracle Cloud images ship their own iptables rules (everything but SSH is
    # rejected) and UFW conflicts with them, so open 80/443 in iptables instead.
    # Ports must ALSO be allowed in the OCI subnet's Security List (console).
    for port in 443 80; do
        iptables -C INPUT -p tcp --dport $port -m state --state NEW -j ACCEPT 2>/dev/null \
            || iptables -I INPUT 5 -p tcp --dport $port -m state --state NEW -j ACCEPT
    done
    netfilter-persistent save
else
    ufw default deny incoming
    ufw default allow outgoing
    ufw allow OpenSSH
    ufw allow 'Nginx Full'
    ufw --force enable
fi
systemctl enable --now fail2ban
dpkg-reconfigure -f noninteractive unattended-upgrades
# Key-only SSH, no root password login. Only applied if a key is installed.
if [ -s /home/$DEPLOY_USER/.ssh/authorized_keys ]; then
    sed -i 's/^#\?PasswordAuthentication .*/PasswordAuthentication no/' /etc/ssh/sshd_config
    sed -i 's/^#\?PermitRootLogin .*/PermitRootLogin prohibit-password/' /etc/ssh/sshd_config
    # Ubuntu 24.04 may socket-activate sshd, in which case reload can fail.
    systemctl reload ssh 2>/dev/null || systemctl restart ssh
fi

cat <<DONE

Server ready.

Next steps:
  1. Create $APP_DIR/shared/.env  (copy .env.production.example) and set:
       DB_DATABASE=$DB_NAME
       DB_USERNAME=$DB_USER
       DB_PASSWORD=$DB_PASS
  2. As $DEPLOY_USER, run the first deploy:  bash deploy.sh
  3. HTTPS:  certbot --nginx -d $DOMAIN -d www.$DOMAIN --redirect -m you@$DOMAIN --agree-tos

The DB password above is shown once and also stored in /home/$DEPLOY_USER/.my.cnf.
DONE
