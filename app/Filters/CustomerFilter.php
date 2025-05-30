<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CustomerFilter
{
    private array $filters;

    /**
     * Create a new class instance.
     */
    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function __invoke(Builder $query): void
    {
        $query->when(!empty($this->filters['q']), function ($query) {
                $query->where(function ($query) {
                    $q = $this->filters['q'];
                    $query->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%");
                });
            });
    }
}
