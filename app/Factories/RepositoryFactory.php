<?php

namespace App\Factories;

use InvalidArgumentException;
use App\Repositories\OrderRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductRepository;
use App\Contracts\TeamScopedRepositoryInterface;

class RepositoryFactory
{
    protected array $map = [
        'order'    => OrderRepository::class,
        'product'  => ProductRepository::class,
        'customer' => CustomerRepository::class,
    ];

    public function make(string $key): TeamScopedRepositoryInterface
    {
        if (! isset($this->map[$key]))  {
            throw new InvalidArgumentException("No repository mapped for type {$key}");
        }

        return app($this->map[$key]);
    }
}
