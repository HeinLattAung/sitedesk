# Deploying SiteDesk to a Linux VPS

This guide takes a fresh **Ubuntu 24.04** VPS to a live HTTPS Laravel site with
zero-downtime deploys, a queue worker, nightly backups and CI/CD.

Stack: **Nginx → PHP 8.4-FPM → Laravel 13 → MySQL 8**, with Supervisor (queue
worker), cron (scheduler + backups), UFW + fail2ban (security), and Certbot (SSL).

```
Browser ──HTTPS──▶ Nginx ──FastCGI──▶ PHP-FPM ──▶ Laravel ──▶ MySQL
                     │                              │
                     └─ /build, /storage (static)   └─ jobs table ◀── queue worker (Supervisor) ──▶ SMTP
```

---

## 0. What you need

- A VPS with Ubuntu 24.04 (1 vCPU / 1 GB RAM is enough). DigitalOcean, Vultr,
  Linode, Hetzner and AWS Lightsail all work. Choose the Singapore region for Thailand.
- A domain name, with an **A record** pointing to the VPS IP (`@` and `www`).
- An SSH key on your computer (`ssh-keygen -t ed25519`), added to the VPS when you create it.
- This repo pushed to GitHub.

## 1. Provision the server (once)

```bash
ssh root@YOUR_SERVER_IP
git clone https://github.com/HeinLattAung/sitedesk.git /tmp/sitedesk
cd /tmp/sitedesk/deploy
DOMAIN=yourdomain.com bash server-setup.sh
```

[`deploy/server-setup.sh`](deploy/server-setup.sh) does all of this:

| Step | Why |
|---|---|
| Installs Nginx, PHP 8.4-FPM + extensions, MySQL 8, Composer, Node 22 | Runtime |
| Creates a `deploy` user with sudo **only** for reloading PHP-FPM and the worker | Least privilege: the app never runs as root |
| Creates the MySQL database + user with a random password | No shared root DB login |
| Creates `/var/www/sitedesk/{releases,shared}` with ACLs for `www-data` | Release-based deploys; PHP can write to storage |
| Installs the Nginx site ([`deploy/nginx/sitedesk.conf`](deploy/nginx/sitedesk.conf)) | Security headers, gzip, asset caching, blocks `.env` / `.git` / stray `.php` |
| Installs the Supervisor worker ([`deploy/supervisor/sitedesk-worker.conf`](deploy/supervisor/sitedesk-worker.conf)) | Sends queued emails; restarts if it crashes |
| Adds cron for `schedule:run` and the 02:30 backup | Scheduled tasks |
| UFW (only SSH/80/443), fail2ban, unattended security upgrades, key-only SSH | Hardening |
| PHP tweaks: OPcache on, `expose_php` off, 8 MB uploads | Performance + security |

Write down the database password it prints at the end.

## 2. Create the production `.env` (once)

```bash
su - deploy
nano /var/www/sitedesk/shared/.env     # paste .env.production.example, fill in values
```

Set `APP_URL`, `DB_PASSWORD` (from step 1), your SMTP details, `SITEDESK_NOTIFY_EMAIL`
and a strong `ADMIN_PASSWORD`. Keep `APP_DEBUG=false`: with debug on, an error page shows your secrets.

## 3. First deploy

```bash
# as deploy
bash /tmp/sitedesk/deploy/deploy.sh main
cd /var/www/sitedesk/current
php artisan key:generate --force    # writes APP_KEY into shared/.env (first time only)
php artisan config:cache
php artisan db:seed --force         # creates the admin user + demo content
sudo supervisorctl restart 'sitedesk-worker:*'
```

What [`deploy/deploy.sh`](deploy/deploy.sh) does on every deploy:

1. `git clone` the branch into a new `releases/<timestamp>` folder
2. Symlink the shared `.env` and `storage/` into it
3. `composer install --no-dev --optimize-autoloader`
4. `npm ci && npm run build`, then delete `node_modules`
5. `php artisan migrate --force`
6. `php artisan optimize` (caches config, routes, views, events)
7. **Atomically swap** the `current` symlink to the new release
8. Reload PHP-FPM (clears OPcache) and restart the queue worker
9. Keep the last 5 releases, delete older ones
10. Check `/up`, the health endpoint

If any step fails, the half-built release is deleted and the live site keeps running the old one.

## 4. HTTPS

```bash
sudo certbot --nginx -d yourdomain.com -d www.yourdomain.com --redirect
```

Certbot adds the 443 server block and the HTTP→HTTPS redirect, and installs a
systemd timer that renews the certificate automatically. Test renewal with
`sudo certbot renew --dry-run`.

## 5. CI/CD with GitHub Actions

[`.github/workflows/deploy.yml`](.github/workflows/deploy.yml) runs the test suite on
every push and pull request. When the tests pass on `main`, it SSHes into the server and runs `deploy.sh`.

Setup:

1. On your computer, create a key just for CI: `ssh-keygen -t ed25519 -f ci_key -N ""`
2. Append `ci_key.pub` to `/home/deploy/.ssh/authorized_keys` on the server.
3. In GitHub → Settings → Secrets and variables → Actions, add these **secrets**:
   - `VPS_HOST`: the server IP
   - `VPS_SSH_KEY`: the contents of `ci_key` (the private key)

4. On the **Variables** tab of the same page, add `DEPLOY_ENABLED` = `true` (until then, CI only runs the tests).

Now `git push origin main` → tests → live in about a minute.

## 6. Day-to-day operations

| Task | Command |
|---|---|
| Roll back to previous release | `bash /var/www/sitedesk/current/deploy/rollback.sh` |
| Laravel logs | `tail -f /var/www/sitedesk/shared/storage/logs/laravel-*.log` |
| Nginx errors | `sudo tail -f /var/log/nginx/error.log` |
| Queue worker status / logs | `sudo supervisorctl status` · `tail -f .../storage/logs/worker.log` |
| Retry failed jobs | `php artisan queue:retry all` |
| Maintenance mode | `php artisan down --secret=let-me-in` / `php artisan up` |
| Run backup now | `bash /var/www/sitedesk/current/deploy/backup.sh` |
| Restore DB backup | `gunzip -c ~/backups/db-XXXX.sql.gz \| mysql sitedesk` |
| Who is attacking SSH? | `sudo fail2ban-client status sshd` |
| Disk / memory | `df -h` · `free -m` · `htop` |

## 7. Troubleshooting

| Symptom | Likely cause → fix |
|---|---|
| **502 Bad Gateway** | PHP-FPM is down or the socket path is wrong → `sudo systemctl status php8.4-fpm`; check `fastcgi_pass` in the Nginx config |
| **500 error, blank page** | Read `storage/logs/laravel-*.log`. Usually a missing `APP_KEY` or wrong DB password; after editing `.env`, run `php artisan config:cache` |
| **"Permission denied" writing logs/sessions** | `sudo setfacl -R -m u:www-data:rwX -m d:u:www-data:rwX /var/www/sitedesk/shared/storage` |
| **Uploaded images 404** | `php artisan storage:link` was not run in the current release |
| **Changes not showing after deploy** | OPcache: deploy.sh reloads PHP-FPM; if you deployed by hand, run `sudo systemctl reload php8.4-fpm` |
| **Contact emails not arriving** | Worker not running (`supervisorctl status`) or bad SMTP settings → `php artisan queue:failed` |
| **413 Request Entity Too Large** | Increase `client_max_body_size` in Nginx and `upload_max_filesize` in PHP |
| **Site behind Cloudflare shows wrong IPs / http links** | Trust proxies in `bootstrap/app.php`: `$middleware->trustProxies(at: '*')` |

## 8. Backups

[`deploy/backup.sh`](deploy/backup.sh) runs every night at 02:30. It dumps MySQL with
`--single-transaction` (no table locks), archives uploaded files, and deletes backups
older than 14 days. To copy backups off the server, install `rclone`, configure a
remote named `offsite` (S3, Backblaze B2, Google Drive…), and the script uploads
to it automatically. **Test a restore at least once.**
