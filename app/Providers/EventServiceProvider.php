<?php

namespace App\Providers;

use App\Events\OrderCreated;
use App\Events\OrderUpdated;
use App\Listeners\ProcessOrderItems;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderCreated::class => [
            ProcessOrderItems::class,
        ],
        OrderUpdated::class => [
            ProcessOrderItems::class,
        ],
    ];
}
