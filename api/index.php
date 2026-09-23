<?php

// 1. Prepare serverless writable directories in /tmp
$storageDirs = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/logs',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// 2. Prepare writable SQLite database in /tmp
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb)) {
    $seededDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($seededDb)) {
        copy($seededDb, $tmpDb);
    } else {
        touch($tmpDb);
    }
}

// Default DB_DATABASE to /tmp/database.sqlite if sqlite is active
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
if ($dbConn === 'sqlite') {
    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// 3. Forward request to Laravel entrypoint
require __DIR__ . '/../public/index.php';
