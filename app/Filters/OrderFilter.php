<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class OrderFilter extends BaseFilter
{
    /**
     * Apply search filter for customers
     */
    protected function applySearch(Builder $query): Builder
    {
        $searchQuery = $this->filters['q'];

        return $query->where(function ($query) use ($searchQuery) {
                $query->where('num', 'like', "%{$searchQuery}");
            });
    }
}
