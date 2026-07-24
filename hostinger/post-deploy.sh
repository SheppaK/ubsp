#!/usr/bin/env bash
# Run from project root on Hostinger (SSH or hPanel Terminal).
set -euo pipefail

ROOT="$(cd "$(dirname "$0")/.." && pwd)"
cd "$ROOT"

echo "==> Checking Vite build..."
if [[ ! -f public/build/manifest.json ]]; then
  echo "ERROR: public/build/manifest.json is missing."
  echo "Run 'npm run build' on your PC, then upload the public/build/ folder."
  exit 1
fi

echo "==> Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Linking public storage (shell symlink — Hostinger disables PHP exec())..."
rm -rf public/storage
ln -sf ../storage/app/public public/storage

echo "==> Caching config/routes/views..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true

echo "==> Setting permissions..."
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "==> Done."
echo "    Health: https://YOUR-DOMAIN/up"
echo "    Assets: public/build/manifest.json OK"
