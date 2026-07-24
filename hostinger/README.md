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

## Health check

Visit `https://yourdomain.com/up` — should return `{"status":"ok"}`.

## Troubleshooting

| Issue | Fix |
|-------|-----|
| 500 error | Check `storage/logs/laravel.log`; ensure `storage/` writable |
| Blank page / no CSS | Run `npm run build` locally and upload `public/build/` |
| `.env` not loading | File must be in project root (same folder as `artisan`) |
| Session / login loops | Set `APP_URL` to exact HTTPS domain; clear config cache |
| Mixed content | `APP_URL` must use `https://` |
