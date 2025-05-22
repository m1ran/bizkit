<?php

namespace App\Factories;

use App\Contracts\TeamScopedRepositoryInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\ProductRepository;
use InvalidArgumentException;

class RepositoryFactory
{
    protected array $map = [
        'product' => ProductRepository::class,
        'customer' => CustomerRepository::class
    ];

    public function make(string $key): TeamScopedRepositoryInterface
    {
        if (! isset($this->map[$key]))  {
            throw new InvalidArgumentException("No repository mapped for type {$key}");
        }

        return app($this->map[$key]);
    }
}
