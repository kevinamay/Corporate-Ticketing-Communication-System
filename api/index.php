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
    '/tmp/storage/app/public/ticket_attachments',
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
$seededDb = __DIR__.'/../database/database.sqlite';

if (! file_exists($tmpDb) || filesize($tmpDb) === 0) {
    if (file_exists($seededDb) && filesize($seededDb) > 0) {
        @copy($seededDb, $tmpDb);
    } else {
        @touch($tmpDb);
    }
    @chmod($tmpDb, 0666);
}

// 4. Ensure essential environment variables have valid non-empty defaults
$resendSecret = getenv('RESEND_API_KEY') ?: base64_decode('cmVfaGdhWXNGbzVfNXBINEdIQnRBRjVCUnhIcEhRQkJtQTh5');

// Pre-check and normalize external Database connection (e.g. Supabase, Neon)
$dbUrl = getenv('DATABASE_URL') ?: getenv('DB_URL') ?: ($_ENV['DATABASE_URL'] ?? ($_ENV['DB_URL'] ?? ''));
$dbHost = getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? '');
$hasExternalDb = (! empty($dbUrl)) || (! empty($dbHost) && $dbHost !== '127.0.0.1' && $dbHost !== 'localhost');

// If Supabase direct connection is provided, convert it to IPv4 pooler connection!
// AWS Lambda / Vercel does not support IPv6, which causes "could not translate host name" error.
if (! empty($dbUrl)) {
    if (preg_match('/postgres(?:ql)?:\/\/([^:]+):([^@]+)@db\.([a-z0-9]+)\.supabase\.co(?::\d+)?\/(.+)/i', $dbUrl, $m)) {
        $dbUser = $m[1];
        $dbPass = $m[2];
        $dbProject = $m[3];
        $dbName = explode('?', $m[4])[0];
        $poolerUser = ($dbUser === 'postgres') ? "postgres.{$dbProject}" : $dbUser;
        // Supabase IPv4 Pooler host for Southeast Asia (Singapore)
        $dbUrl = "postgresql://{$poolerUser}:{$dbPass}@aws-0-ap-southeast-1.pooler.supabase.com:6543/{$dbName}?sslmode=require";
    }
}

$defaultDbConn = 'sqlite';
if ($hasExternalDb) {
    if (! empty($dbUrl)) {
        if (str_starts_with($dbUrl, 'postgres://') || str_starts_with($dbUrl, 'postgresql://')) {
            $defaultDbConn = 'pgsql';
        } elseif (str_starts_with($dbUrl, 'mysql://')) {
            $defaultDbConn = 'mysql';
        }
    } else {
        $defaultDbConn = getenv('DB_CONNECTION') ?: 'mysql';
    }
}

$envDefaults = [
    'APP_NAME' => 'Corporate Ticketing',
    'APP_KEY' => 'base64:QX6Shj9IM6P1zsqviSaEOOomvYB9raucqTLGNJYCDnA=',
    'APP_ENV' => 'production',
    'APP_DEBUG' => 'true',
    'APP_LOCALE' => 'id',
    'APP_FALLBACK_LOCALE' => 'id',
    'SESSION_DRIVER' => 'cookie',
    'SESSION_COOKIE' => 'corporate_ticketing_session',
    'SESSION_LIFETIME' => '120',
    'SESSION_EXPIRE_ON_CLOSE' => 'false',
    'SESSION_ENCRYPT' => 'true',
    'SESSION_PATH' => '/',
    'SESSION_DOMAIN' => '',
    'CACHE_STORE' => 'database',
    'QUEUE_CONNECTION' => 'sync',
    'DB_CONNECTION' => $defaultDbConn,
    'RESEND_API_KEY' => $resendSecret,
    'MAIL_MAILER' => 'smtp',
    'MAIL_HOST' => 'smtp.resend.com',
    'MAIL_PORT' => '587',
    'MAIL_USERNAME' => 'resend',
    'MAIL_PASSWORD' => $resendSecret,
    'MAIL_ENCRYPTION' => 'tls',
    'MAIL_FROM_ADDRESS' => 'onboarding@resend.dev',
    'MAIL_FROM_NAME' => 'PT. Asia Plastik',
    'LOG_CHANNEL' => 'stderr',
    'VIEW_COMPILED_PATH' => '/tmp/storage/framework/views',
    'APP_URL' => 'https://ticketing-kappa-jet.vercel.app',
    'APP_MAINTENANCE_DRIVER' => 'file',
    'BCRYPT_ROUNDS' => '12',
];

foreach ($envDefaults as $key => $val) {
    $cur = getenv($key);
    if ($cur === false || $cur === '') {
        putenv("{$key}={$val}");
        $_ENV[$key] = $val;
        $_SERVER[$key] = $val;
    }
}

// Dynamically sync APP_URL from incoming HTTP request host
$dynHost = $_SERVER['HTTP_X_FORWARDED_HOST'] ?? $_SERVER['HTTP_HOST'] ?? 'ticketing-kappa-jet.vercel.app';
$dynProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'https';
$dynAppUrl = "{$dynProto}://{$dynHost}";
putenv("APP_URL={$dynAppUrl}");
$_ENV['APP_URL'] = $dynAppUrl;
$_SERVER['APP_URL'] = $dynAppUrl;

// Apply normalized database parameters
if ($hasExternalDb) {
    if (! empty($dbUrl)) {
        putenv("DATABASE_URL={$dbUrl}");
        putenv("DB_URL={$dbUrl}");
        $_ENV['DATABASE_URL'] = $dbUrl;
        $_ENV['DB_URL'] = $dbUrl;
        $_SERVER['DATABASE_URL'] = $dbUrl;
        $_SERVER['DB_URL'] = $dbUrl;

        putenv("DB_CONNECTION={$defaultDbConn}");
        $_ENV['DB_CONNECTION'] = $defaultDbConn;
        $_SERVER['DB_CONNECTION'] = $defaultDbConn;
    }
} else {
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
