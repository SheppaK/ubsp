# UBSP — Hostinger deployment (public_html)

This project is set up for **shared Hostinger hosting** with the Laravel app uploaded inside `public_html`.

## Recommended layout on Hostinger

Upload the **entire project** into `public_html` so it looks like:

```
public_html/
├── .htaccess          ← routes traffic to public/ (included in repo)
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   ├── .htaccess
│   ├── index.php
│   └── build/         ← Vite assets (run npm run build before upload)
├── resources/
├── routes/
├── storage/
├── vendor/            ← run composer install on server OR upload after local install
├── .env               ← create on server (see .env.hostinger.example)
└── artisan
```

**Better (if hPanel allows):** set the domain document root to `public_html/public` instead of using the root `.htaccess` redirect.

## Before upload (on your PC)

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
```

Upload `public/build/` — it is required in production (no Vite dev server on Hostinger).

**Important:** `public/build/` is not in git. After every `npm run build`, upload this folder:

```
public/build/
├── manifest.json
└── assets/
    ├── app-XXXXX.css
    └── app-XXXXX.js
```

Use File Manager or FTP: upload into `public_html/public/build/` on the server.

## Server setup

1. **PHP version:** 8.2 or 8.3 in hPanel → Advanced → PHP Configuration.
2. **MySQL:** create database + user in hPanel; copy credentials into `.env`.
3. **`.env`:** copy `.env.hostinger.example` to `.env` on the server and fill in values.
4. **Permissions:** `storage/` and `bootstrap/cache/` must be writable (755 or 775).

## After upload

### Option A — SSH / Terminal (preferred)

```bash
cd ~/domains/yourdomain.com/public_html
bash hostinger/post-deploy.sh
```

### Option B — No SSH

1. Edit `hostinger/post-deploy.php` and set a strong `$expectedToken`.
2. Visit `https://yourdomain.com/hostinger/post-deploy.php?token=YOUR_TOKEN` once.
3. **Delete** `hostinger/post-deploy.php` immediately after.

### Manual artisan commands

```bash
php artisan key:generate   # only if APP_KEY is empty
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Production `.env` essentials

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
```

## KC Pay on production

- Configure credentials in **Admin → KC Pay Settings** (stored in database).
- Callback URL: `https://yourdomain.com/api/kcpay/callback`
- Server-to-server calls to KC Pay run from Hostinger (usually more reliable than mobile hotspot).

## Flattened layout (alternative)

If you moved everything from `public/` into `public_html/` root:

1. Replace `public_html/index.php` with `hostinger/public_html.index.php`
2. Replace `public_html/.htaccess` with `hostinger/public_html.htaccess`
3. Copy `hostinger/.user.ini` to `public_html/.user.ini`

## Error: Table 'cache' doesn't exist

Migrations have not been run yet. Fix with **one** of these:

### A — SSH / hPanel Terminal (recommended)

```bash
cd ~/domains/yourdomain.com/public_html
php artisan migrate --force
php artisan db:seed --force   # optional: demo admin user
php artisan config:cache
```

### B — Quick env workaround (no migrate yet)

In server `.env` set:

```env
CACHE_STORE=file
```

Then run `php artisan config:clear` or delete `bootstrap/cache/config.php`.

### C — phpMyAdmin only

Import `hostinger/quick-fix-cache.sql` into your database, then still run full migrate when possible.

## Deployment panel (super-admin)

After deploy, log in as **super-admin** and open **Deployment** in the sidebar (`/admin/deployment`):

- **Git pull** — requires a git clone on the server and shell functions enabled
- **Run migrations**, clear/cache config, routes, views
- **Link storage** — works without PHP `exec()`

Set in `.env`:

```env
DEPLOY_ENABLED=true
DEPLOY_GIT_ENABLED=true
DEPLOY_GIT_BRANCH=main
```

Set `DEPLOY_ENABLED=false` to hide the panel entirely.

## Health check

Visit `https://yourdomain.com/up` — should return `{"status":"ok"}`.

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 error | Check `storage/logs/laravel.log`; ensure `storage/` writable |
| Blank page / no CSS | Upload `public/build/` (run `npm run build` locally first) |
| Vite manifest not found | Same — upload entire `public/build/` folder to `public_html/public/build/` |
| storage:link exec() error | Run: `ln -sf ../storage/app/public public/storage` in SSH |
| `.env` not loading | File must be in project root (same folder as `artisan`) |
| Session / login loops | Set `APP_URL` to exact HTTPS domain; clear config cache |
| Mixed content | `APP_URL` must use `https://` |
