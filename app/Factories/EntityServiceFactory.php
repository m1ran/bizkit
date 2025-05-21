<?php

namespace App\Factories;

use App\Contracts\EntityServiceInterface;
use App\Services\CustomerService;
use InvalidArgumentException;

class EntityServiceFactory
{
    protected array $map = [
        'customer' => CustomerService::class
    ];

    public function make(string $key): EntityServiceInterface
    {
        if (! isset($this->map[$key]))  {
            throw new InvalidArgumentException("No service mapped for type {$key}");
        }

        return app($this->map[$key]);
    }
}
