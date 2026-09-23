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
    '/tmp/storage/app/public/avatars',
    '/tmp/storage/app/private',
    '/tmp/storage/app/private/livewire-tmp',
    '/tmp/storage/app/livewire-tmp',
    '/tmp/livewire-tmp',
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
    if (! is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// 2. Redirect bootstrap caches to /tmp so Laravel writes fresh production manifests
putenv('APP_PACKAGES_CACHE=/tmp/packages.php');
putenv('APP_SERVICES_CACHE=/tmp/services.php');
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_ENV['APP_SERVICES_CACHE'] = '/tmp/services.php';
$_SERVER['APP_PACKAGES_CACHE'] = '/tmp/packages.php';
$_SERVER['APP_SERVICES_CACHE'] = '/tmp/services.php';

// 3. Prepare writable SQLite database in /tmp
$tmpDb = '/tmp/database.sqlite';
if (! file_exists($tmpDb) || filesize($tmpDb) === 0) {
    $seededDb = __DIR__.'/../database/database.sqlite';
    if (file_exists($seededDb) && filesize($seededDb) > 0) {
        copy($seededDb, $tmpDb);
    } else {
        touch($tmpDb);
    }
    @chmod($tmpDb, 0666);
}

// 4. Ensure essential environment variables have valid non-empty defaults
$envDefaults = [
    'APP_NAME' => 'Corporate Ticketing',
    'APP_KEY' => 'base64:QX6Shj9IM6P1zsqviSaEOOomvYB9raucqTLGNJYCDnA=',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'false',
    'SESSION_DRIVER' => 'file',
    'SESSION_COOKIE' => 'corporate_ticketing_session',
    'CACHE_STORE' => 'database',
    'QUEUE_CONNECTION' => 'database',
    'DB_CONNECTION' => 'sqlite',
    'FILESYSTEM_DISK' => 'local',
    'MAIL_MAILER' => 'log',
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'APP_MAINTENANCE_DRIVER' => 'file',
];

foreach ($envDefaults as $key => $val) {
    $cur = getenv($key);
    if ($cur === false || $cur === '') {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
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

// 5. Forward request to Laravel public/index.php with debug catch
try {
    require __DIR__.'/../public/index.php';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<h1>Vercel Deployment Error</h1>';
    echo '<p><strong>'.htmlspecialchars($e->getMessage()).'</strong></p>';
    echo '<p>'.htmlspecialchars($e->getFile()).' (Line '.$e->getLine().')</p>';
    echo '<pre>'.htmlspecialchars($e->getTraceAsString()).'</pre>';
}
