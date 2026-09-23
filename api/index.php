<?php

// Explicitly set Vercel env indicators
putenv('VERCEL=1');
$_ENV['VERCEL'] = '1';
$_SERVER['VERCEL'] = '1';

// 1. Prepare serverless writable directories in /tmp
$storageDirs = [
    '/tmp/storage',
    '/tmp/storage/app',
    '/tmp/storage/app/public',
    '/tmp/storage/framework',
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/framework/sessions',
    '/tmp/storage/framework/testing',
    '/tmp/storage/logs',
    '/tmp/views',
];

foreach ($storageDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Redirect bootstrap caches to /tmp and copy pre-discovered files if present
$packagesCache = __DIR__ . '/../bootstrap/cache/packages.php';
if (file_exists($packagesCache) && !file_exists('/tmp/packages.php')) {
    copy($packagesCache, '/tmp/packages.php');
}
$servicesCache = __DIR__ . '/../bootstrap/cache/services.php';
if (file_exists($servicesCache) && !file_exists('/tmp/services.php')) {
    copy($servicesCache, '/tmp/services.php');
}

putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
putenv('APP_CONFIG_CACHE=/tmp/config.php');
putenv('APP_ROUTES_CACHE=/tmp/routes.php');
putenv('APP_EVENTS_CACHE=/tmp/events.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/events.php';
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_SERVER['APP_CONFIG_CACHE'] = '/tmp/config.php';
$_SERVER['APP_ROUTES_CACHE'] = '/tmp/routes.php';
$_SERVER['APP_EVENTS_CACHE'] = '/tmp/events.php';

// 3. Prepare writable SQLite database in /tmp
$tmpDb = '/tmp/database.sqlite';
if (!file_exists($tmpDb) || filesize($tmpDb) === 0) {
    $seededDb = __DIR__ . '/../database/database.sqlite';
    if (file_exists($seededDb) && filesize($seededDb) > 0) {
        copy($seededDb, $tmpDb);
    } else {
        touch($tmpDb);
    }
    @chmod($tmpDb, 0666);
}

// Ensure APP_KEY is always set and never empty
$appKey = getenv('APP_KEY') ?: ($_ENV['APP_KEY'] ?? '');
if (empty($appKey)) {
    $fallbackKey = 'base64:QX6Shj9IM6P1zsqviSaEOOomvYB9raucqTLGNJYCDnA=';
    putenv("APP_KEY={$fallbackKey}");
    $_ENV['APP_KEY'] = $fallbackKey;
    $_SERVER['APP_KEY'] = $fallbackKey;
}

// Ensure Database connection works seamlessly on Vercel
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '');
$dbConn = getenv('DB_CONNECTION') ?: ($_ENV['DB_CONNECTION'] ?? 'sqlite');
if (empty($dbConn) || $dbConn === 'sqlite' || $dbHost === '127.0.0.1' || $dbHost === 'localhost' || empty($dbHost)) {
    putenv('DB_CONNECTION=sqlite');
    $_ENV['DB_CONNECTION'] = 'sqlite';
    $_SERVER['DB_CONNECTION'] = 'sqlite';

    putenv("DB_DATABASE={$tmpDb}");
    $_ENV['DB_DATABASE'] = $tmpDb;
    $_SERVER['DB_DATABASE'] = $tmpDb;
}

// Set storage paths for serverless
putenv('VIEW_COMPILED_PATH=/tmp/storage/framework/views');
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

// 4. Forward request to Laravel public/index.php with debug catch
try {
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    http_response_code(500);
    echo "<h1>Vercel Deployment Error</h1>";
    echo "<p><strong>" . htmlspecialchars($e->getMessage()) . "</strong></p>";
    echo "<p>" . htmlspecialchars($e->getFile()) . " (Line " . $e->getLine() . ")</p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
