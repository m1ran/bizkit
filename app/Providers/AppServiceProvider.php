<?php

namespace App\Providers;

use App\Factories\RepositoryFactory;
use App\Factories\EntityServiceFactory;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\ServiceProvider;
use App\Observers\AuditableObserver;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(RepositoryFactory::class);
        $this->app->singleton(EntityServiceFactory::class);

        Relation::morphMap([
            'order'    => Order::class,
            'product'  => Product::class,
            'customer' => Customer::class,
        ]);

        if ($this->app->environment('local') && class_exists(\Laravel\Telescope\TelescopeServiceProvider::class)) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Order::observe(AuditableObserver::class);
        Product::observe(AuditableObserver::class);
        Customer::observe(AuditableObserver::class);
    }
}
