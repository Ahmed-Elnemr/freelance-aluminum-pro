<?php

namespace App\Providers;

use App\Exceptions\CustomException;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ExceptionHandler::class, CustomException::class);
        $this->app->bind(
            \App\Repositories\Contracts\AuthRepositoryInterface::class,
            \App\Repositories\Eloquent\AuthRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(UserObserver::class);
        Gate::define('use-translation-manager', function (?User $user) {
            // Your authorization logic
            return 1;
        });

        Schema::defaultStringLength(191);
        date_default_timezone_set('Asia/Riyadh');
    }
}
