<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Gate::define('access-hcm-master', function ($user) {
            return ((int) $user->department_id === 2)
                || ((int) $user->department_id === 4)
                || ($user->department && (
                    str_contains(strtolower($user->department->name), 'human resources') ||
                    str_contains(strtolower($user->department->name), 'hr')
                ));
        });
    }
}
