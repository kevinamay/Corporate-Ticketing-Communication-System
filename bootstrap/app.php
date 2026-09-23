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
            'livewire/upload-file',
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
        $app['config']->set('session.driver', 'cookie');
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
    if ($isVercel) {
        $app['config']->set('livewire.temporary_file_upload.disk', 'local');
        $app['config']->set('livewire.temporary_file_upload.directory', 'livewire-tmp');
    }
});

return $app;
