<?php

namespace App\Factories;

use App\Contracts\InstanceServiceInterface;
use App\Services\CustomerService;
use InvalidArgumentException;

class ServiceFactory
{
    protected array $map = [
        'customer' => CustomerService::class
    ];

    public function make(string $key): InstanceServiceInterface
    {
        if (! isset($this->map[$key]))  {
            throw new InvalidArgumentException("No service mapped for type {$key}");
        }

        return app($this->map[$key]);
    }
}
