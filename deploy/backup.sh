#!/usr/bin/env bash
#
# Nightly backup (cron, 02:30): MySQL dump + uploaded files, kept 14 days.
# DB credentials come from ~/.my.cnf (created by server-setup.sh).
#
# A backup on the same disk is not enough: copy BACKUP_DIR off the server,
# e.g. with rclone to S3 / Backblaze / Google Drive (see DEPLOYMENT.md).
set -euo pipefail

BACKUP_DIR="${BACKUP_DIR:-$HOME/backups}"
DB=sitedesk
STAMP="$(date +%Y%m%d-%H%M)"
mkdir -p "$BACKUP_DIR"

mysqldump --single-transaction --quick --no-tablespaces "$DB" | gzip > "$BACKUP_DIR/db-$STAMP.sql.gz"
tar -czf "$BACKUP_DIR/uploads-$STAMP.tar.gz" -C /var/www/sitedesk/shared/storage/app public

find "$BACKUP_DIR" -type f -mtime +14 -delete

# Optional off-site copy if rclone is configured with a remote named "offsite".
if command -v rclone >/dev/null && rclone listremotes | grep -q '^offsite:'; then
    rclone copy "$BACKUP_DIR" offsite:sitedesk-backups --max-age 25h
fi

echo "$(date -Is) backup ok: db-$STAMP.sql.gz"
