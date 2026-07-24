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

require $root.'/vendor/autoload.php';

$app = require_once $root.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$commands = [
    'migrate --force',
    'storage:link',
    'config:cache',
    'route:cache',
    'view:cache',
];

header('Content-Type: text/plain; charset=utf-8');

foreach ($commands as $command) {
    echo "Running: php artisan {$command}\n";
    $status = $kernel->call($command);
    echo $kernel->output();
    echo "Exit: {$status}\n\n";
}

echo "Post-deploy complete. DELETE hostinger/post-deploy.php from the server now.\n";
