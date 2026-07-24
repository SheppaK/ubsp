#!/usr/bin/env bash
# Run from project root on Hostinger (SSH or hPanel Terminal).
set -euo pipefail

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Linking public storage..."
php artisan storage:link --force 2>/dev/null || php artisan storage:link

echo "==> Caching config/routes/views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

echo "==> Setting permissions..."
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "==> Done. Visit your site and confirm /up returns OK."
