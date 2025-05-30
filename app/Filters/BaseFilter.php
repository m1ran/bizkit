<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseFilter
{
    const STANDARD_FILTERS = ['q', 'status', 'date_from', 'date_to'];

    protected array $filters;

    /**
     * Create a new class instance.
     */
    public function __construct(array $filters)
    {
        $this->filters = array_filter($filters);
    }

    /**
     * Apply all filters to the query
     */
    public function apply(Builder $query): Builder
    {
        return $query
            ->when($this->hasSearch(), fn($q) => $this->applySearch($q))
            ->when($this->hasStatus(), fn($q) => $this->applyStatus($q))
            ->when($this->hasDateRange(), fn($q) => $this->applyDateRange($q))
            ->when($this->hasCustomFilters(), fn($q) => $this->applyCustomFilters($q));
    }

    /**
     * Apply search filter should be made in child
     */
    abstract protected function applySearch(Builder $query): Builder;

    /**
     * Apply custom filters specific to each model
     */
    protected function applyCustomFilters(Builder $query): Builder
    {
        return $query; // some actions in future
    }

    /**
     * Apply status filter
     */
    protected function applyStatus(Builder $query): Builder
    {
        return $query->where('status_id', $this->filters['status']);
    }

    /**
     * Apply date range filter
     */
    protected function applyDateRange(Builder $query): Builder
    {
        if (isset($this->filters['date_from'])) {
            $query->where('created_at', '>=', $this->filters['date_from']);
        }

        if (isset($this->filters['date_to'])) {
            $query->where('created_at', '<=', $this->filters['date_to']);
        }

        return $query;
    }

    protected function hasSearch(): bool
    {
        return !empty($this->filters['q']);
    }

    /**
     * Check if status filter is present
     */
    protected  function hasStatus(): bool
    {
        return !empty($this->filters['status']);
    }

    /**
     * Check if date range filter is present
     */
    protected function hasDateRange(): bool
    {
        return !empty($this->filters['date_from']) || !empty($this->filters['date_to']);
    }

    /**
     * Check if custom filters are present
     */
    protected function hasCustomFilters(): bool
    {
        $customFilters = array_diff_key($this->filters, array_flip(self::STANDARD_FILTERS));

        return !empty($customFilters);
    }

    /**
     * Get filter value
     */
    protected function getFilter(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }

    /**
     * Check if specific filter exists
     */
    protected function hasFilter(string $key): bool
    {
        return !empty($this->filters[$key]);
    }

    /**
     * Backward compatibility
     */
    public function __invoke(Builder $query): void
    {
        $this->apply($query);
    }
}
