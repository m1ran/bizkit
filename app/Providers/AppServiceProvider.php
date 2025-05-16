<?php

namespace App\Providers;

use App\Models\Customer;
use Illuminate\Support\ServiceProvider;
use App\Observers\CustomerObserver;
use App\Repositories\Eloquent\CustomerRepository;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CustomerRepositoryInterface::class, CustomerRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Customer::observe(CustomerObserver::class);
    }
}
