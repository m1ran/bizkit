<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use App\Contracts\TeamScopedRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
            // limit products by IDs if provided
            ->when(!empty($filters['ids']), function ($query) use ($filters) {
                $query->whereIn('id', $filters['ids']);
            })
            // exclude products by IDs if provided
            ->when(!empty($filters['exclude']), function ($query) use ($filters) {
                $query->whereNotIn('id', $filters['exclude']);
            })
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $query->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('sku', 'like', "%{$filters['q']}%");
            })
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
                'price',
                'quantity',
                'description',
            ])
            ->latest()
            ->where('team_id', $teamId)
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $q = $filters['q'];
                $query->where(function ($query) use ($q) {
                    $query->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    /**
     * Find a product by its ID for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return Product
     */
    public function findByTeam(int $teamId, int $id): Product
    {
        return Product::where('team_id', $teamId)->find($id);
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
}
