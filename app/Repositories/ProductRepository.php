<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\TeamScopedRepositoryInterface;
use App\Filters\ProductFilter;
use App\Models\ProductCategory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ProductRepository implements TeamScopedRepositoryInterface
{
    /**
     * Get products for a specific team with optional filters.
     *
     * @param int $teamId
     * @param array $filters
     * @param int $limit
     * @return Collection
     */
    public function getByTeam(int $teamId, array $filters = [], $limit = 10): Collection
    {
        return Product::query()
            ->select([
                'id',
                'name',
                'sku',
                'price',
                'quantity',
            ])
            ->orderBy('name')
            ->where('team_id', $teamId)
            ->when(!empty($filters), fn($query) => $this->applyFilters($query, $filters))
            ->get();
    }

    /**
     * Get paginated products for a specific team with optional filters.
     *
     * @param int $teamId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByTeamPaginated(int $teamId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Product::query()
            ->select([
                'id',
                'name',
                'sku',
                'cost',
                'price',
                'quantity',
                'category_id',
                'description',
            ])
            ->latest()
            ->where('team_id', $teamId)
            ->when(!empty($filters), fn($query) => $this->applyFilters($query, $filters))
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    /**
     * Find a product by its ID for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @param array $relations
     * @return Product
     */
    public function findByTeam(int $teamId, int $id, array $relations = []): Product
    {
        return Product::where('team_id', $teamId)
            ->when(!empty($relations), fn($query) => $query->with($relations))
            ->findOrFail($id);
    }

    /**
     * Create a new product for a specific team.
     *
     * @param int $teamId
     * @param array $data
     * @return Product
     */
    public function createForTeam(int $teamId, array $data): Product
    {
        return Product::create(array_merge($data, ['team_id' => $teamId]));
    }

    /**
     * Update a product for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @param array $data
     * @return Product
     */
    public function updateForTeam(int $teamId, int $id, array $data): Product
    {
        $product = Product::where('team_id', $teamId)->findOrFail($id);
        $product->update($data);

        return $product;
    }

    /**
     * Delete a product for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return bool
     */
    public function deleteForTeam(int $teamId, int $id): bool
    {
        $product = Product::where('team_id', $teamId)->findOrFail($id);

        return $product->delete();
    }

    /**
     * Apply filters to the query
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        return (new ProductFilter($filters))->apply($query);
    }
}
