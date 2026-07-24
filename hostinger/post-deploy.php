<?php
/**
 * Hostinger post-deploy (run via browser ONCE after upload, then delete this file).
 * URL: https://yourdomain.com/hostinger/post-deploy.php?token=CHANGE_ME
 */
declare(strict_types=1);

$expectedToken = 'CHANGE_ME_BEFORE_UPLOAD';

if (($_GET['token'] ?? '') !== $expectedToken) {
    http_response_code(403);
    exit('Forbidden');
}

$root = dirname(__DIR__);
chdir($root);

header('Content-Type: text/plain; charset=utf-8');

if (! is_file($root.'/public/build/manifest.json')) {
    exit("ERROR: Upload public/build/ first (run npm run build on your PC).\n");
}

require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

// Hostinger disables PHP exec(); artisan storage:link fails — use symlink() directly.
$storagePublic = $root.'/storage/app/public';
$publicStorage = $root.'/public/storage';

if (! is_dir($storagePublic)) {
    mkdir($storagePublic, 0755, true);
}

if (is_link($publicStorage)) {
    unlink($publicStorage);
} elseif (is_dir($publicStorage)) {
    rmdir($publicStorage);
}

if (! symlink($storagePublic, $publicStorage)) {
    echo "WARNING: Could not create storage symlink. Run in SSH:\n";
    echo "  ln -sf ../storage/app/public public/storage\n\n";
} else {
    echo "Storage symlink created: public/storage -> storage/app/public\n\n";
}

$commands = [
    'migrate --force',
    'config:clear',
    'config:cache',
    'route:cache',
    'view:cache',
];

foreach ($commands as $command) {
    echo "Running: php artisan {$command}\n";
    $status = $kernel->call($command);
    echo $kernel->output();
    echo "Exit: {$status}\n\n";
}

echo "Post-deploy complete. DELETE hostinger/post-deploy.php from the server now.\n";
