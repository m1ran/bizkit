<?php

namespace App\Providers;

use App\Factories\RepositoryFactory;
use App\Factories\ServiceFactory;
use App\Models\Customer;
use Illuminate\Support\ServiceProvider;
use App\Observers\AuditableObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ServiceFactory::class);
        $this->app->singleton(RepositoryFactory::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Customer::observe(AuditableObserver::class);
    }
}
