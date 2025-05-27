<?php

namespace App\Factories;

use InvalidArgumentException;
use App\Services\OrderService;
use App\Services\ProductService;
use App\Services\CustomerService;
use App\Contracts\EntityServiceInterface;

class EntityServiceFactory
{
    protected array $map = [
        'order'    => OrderService::class,
        'product'  => ProductService::class,
        'customer' => CustomerService::class,
    ];

    public function make(string $key): EntityServiceInterface
    {
        if (! isset($this->map[$key]))  {
            throw new InvalidArgumentException("No service mapped for type {$key}");
        }

        return app($this->map[$key]);
    }
}
