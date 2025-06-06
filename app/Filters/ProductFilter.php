<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ProductFilter extends BaseFilter
{
    /**
     * Apply search filter for customers
     */
    protected function applySearch(Builder $query): Builder
    {
        $searchQuery = $this->filters['q'];

        return $query->where(function ($query) use ($searchQuery) {
                $query->where('sku', 'like', "{$searchQuery}%")
                    ->orWhere('name', 'like', "{$searchQuery}%");
            });
    }

    /**
     * Apply product custom filters
     */
    protected function applyCustomFilters(Builder $query): Builder
    {
        return $query
            ->when($this->hasFilter('category'), fn($q) => $q->where('category_id', $this->getFilter('category')));
    }
}
