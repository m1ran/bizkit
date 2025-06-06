<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class CustomerFilter extends BaseFilter
{
    /**
     * Apply search filter for customers
     */
    protected function applySearch(Builder $query): Builder
    {
        $searchQuery = $this->filters['q'];

        return $query->where(function ($query) use ($searchQuery) {
                $query->where('first_name', 'like', "{$searchQuery}%")
                    ->orWhere('last_name', 'like', "{$searchQuery}%")
                    ->orWhere('email', 'like', "{$searchQuery}%")
                    ->orWhere('phone', 'like', "%{$searchQuery}%");
            });
    }
}
