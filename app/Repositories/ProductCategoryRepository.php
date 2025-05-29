<?php

namespace App\Repositories;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

class ProductCategoryRepository
{
    /**
     * Get  product categories for a specific team.
     *
     * @param int $teamId
     * @return Collection
     */
    public function getByTeam(int $teamId): Collection
    {
        return ProductCategory::query()
            ->select(['id', 'name'])
            ->where('team_id', $teamId)
            ->orderBy('name')
            ->limit(100)
            ->get();
    }

    /**
     * Create a new product category for a specific team.
     *
     * @param int $teamId
     * @param array $data
     * @return ProductCategory
     */
    public function createForTeam(int $teamId, array $data): ProductCategory
    {
        return ProductCategory::create(array_merge($data, ['team_id' => $teamId]));
    }

    /**
     * Update a product category for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @param array $data
     * @return ProductCategory
     */
    public function updateForTeam(int $teamId, int $id, array $data): ProductCategory
    {
        $category = ProductCategory::where('team_id', $teamId)->findOrFail($id);
        $category->update($data);

        return $category;
    }

    /**
     * Delete a product category for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return bool
     */
    public function deleteForTeam(int $teamId, int $id): bool
    {
        $category = ProductCategory::where('team_id', $teamId)->findOrFail($id);

        return $category->delete();
    }
}
