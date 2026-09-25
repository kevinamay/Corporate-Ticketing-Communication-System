<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            'livewire/*',
        ]);
        $middleware->encryptCookies(except: [
            'locale',
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

$isVercel = is_dir('/var/task') || ! is_writable($app->storagePath()) || getenv('VERCEL') || isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL']);

if ($isVercel) {
    $app->useStoragePath('/tmp/storage');
}

$app->booting(function () use ($app, $isVercel) {
    if (empty($app['config']['app.maintenance.driver'])) {
        $app['config']->set('app.maintenance.driver', 'file');
    }
    if ($isVercel) {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite.database', '/tmp/database.sqlite');
        $app['config']->set('session.driver', 'file');
        $app['config']->set('session.files', '/tmp/storage/framework/sessions');
        $app['config']->set('session.cookie', 'corporate_ticketing_session');
        $app['config']->set('session.lifetime', 120);
        $app['config']->set('session.expire_on_close', false);
        $app['config']->set('session.domain', null);
        $app['config']->set('session.path', '/');
        $app['config']->set('livewire.temporary_file_upload.disk', 'local');
        $app['config']->set('livewire.temporary_file_upload.directory', 'livewire-tmp');
    } elseif (empty($app['config']['session.driver'])) {
        $app['config']->set('session.driver', 'database');
    }
    if (empty($app['config']['cache.default'])) {
        $app['config']->set('cache.default', 'database');
    }
    if (empty($app['config']['queue.default'])) {
        $app['config']->set('queue.default', 'database');
    }
    if (empty($app['config']['database.default'])) {
        $app['config']->set('database.default', 'sqlite');
    }
    $bcryptRounds = (int) ($app['config']['hashing.bcrypt.rounds'] ?? 12);
    if ($bcryptRounds < 4) {
        $app['config']->set('hashing.bcrypt.rounds', 12);
    }
});

return $app;
