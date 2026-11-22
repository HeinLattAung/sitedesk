# SiteDesk: business website + CMS (Laravel 13)

A complete small-business website with its own admin panel, built with **PHP 8.4 / Laravel 13**,
**Blade + Tailwind CSS** and **MySQL**, deployed to an **Ubuntu 24.04 server (Nginx, PHP-FPM, MySQL)**
with zero-downtime releases and GitHub Actions CI. The same scripts provision any cloud VPS.

> Currently running on my own Ubuntu 24.04 server (WSL); a public demo will follow on a cloud VPS ·
> Deployment guide: [DEPLOYMENT.md](DEPLOYMENT.md) · QA report: [docs/QA-REPORT.md](docs/QA-REPORT.md)

## Features

**Public website (responsive, mobile-first)**
- Home page with services and the latest blog posts
- Blog with search, pagination, SEO-friendly slugs, reading time and related posts
- Contact form with validation, a honeypot, and rate limiting (5/min per IP). Each inquiry is
  saved to the database, and the owner gets a **queued** email.

**Admin panel** (`/admin`, session login with throttling)
- Dashboard with stats and the latest inquiries
- Posts: create, edit and delete; draft / publish / schedule; cover image upload (validated; the old file is removed on replace or delete)
- Services: create, edit, delete, reorder and hide; the home-page cache is cleared on save
- Inquiries inbox: unread badge, filter, auto mark-as-read

**Server & DevOps** (see [`deploy/`](deploy))
- `server-setup.sh`: provisions a fresh Ubuntu VPS: Nginx, PHP 8.4-FPM, MySQL 8, Supervisor, UFW, fail2ban, unattended upgrades, key-only SSH
- `deploy.sh`: release-folder deploys with an atomic symlink swap, auto-cleanup of failed builds, and a health check
- `rollback.sh`: switch back to the previous release in one command
- `backup.sh`: nightly MySQL + uploads backup with 14-day retention and optional off-site copy (rclone)
- Nginx config with security headers, gzip, long-term caching for hashed assets, and blocks on `.env` and `.php` in uploads
- GitHub Actions: run tests on every push, deploy to the VPS over SSH when `main` is green

## Laravel features used

Eloquent models, relationships, query scopes and model events · Form Request validation ·
resource controllers and route-model binding by slug · `auth` / `guest` / `throttle` middleware ·
queued notifications (database queue) · file storage on the `public` disk · cache ·
anonymous Blade components and partials · factories and seeders · **30 feature tests** (PHPUnit).

## Security

CSRF on every form · Blade escaping (post bodies are escaped, never rendered as raw HTML) ·
image-only uploads (MIME + size validated) · login + contact rate limiting · session regenerated on login ·
production seeder refuses to create an admin without a password · `APP_DEBUG=false` in production.

## Run locally

Requires PHP 8.3+, Composer and Node 20+.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed      # SQLite by default; creates admin@example.com / password
php artisan storage:link
npm install && npm run build
php artisan serve               # http://localhost:8000  (admin: /login)
```

Run the tests:

```bash
php artisan test
```

## Project structure

```
app/Http/Controllers/        Public controllers (Home, Blog, Contact) + Auth
app/Http/Controllers/Admin/  Dashboard, Posts, Services, Inquiries
app/Http/Requests/           Validation rules
app/Models/                  Post, Service, Inquiry, User
app/Notifications/           NewInquiryReceived (queued mail)
resources/views/             Blade views (components/layouts = site + admin layouts)
deploy/                      Server provisioning, Nginx, Supervisor, deploy/rollback/backup scripts
.github/workflows/           CI tests + SSH deploy
tests/Feature/               Public site, contact form, admin
```
