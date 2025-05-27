<?php

namespace App\Repositories;

use App\Contracts\TeamScopedRepositoryInterface;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository implements TeamScopedRepositoryInterface
{
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

    public function findByTeam(int $teamId, int $id): Product
    {
        return Product::where('team_id', $teamId)->find($id);
    }

    public function createForTeam(int $teamId, array $data): Product
    {
        return DB::transaction(function () use ($teamId, $data) {
            return Product::create(array_merge($data, ['team_id' => $teamId]));
        });
    }

    public function updateForTeam(int $teamId, int $id, array $data): Product
    {
        return DB::transaction(function () use ($teamId, $id, $data) {
            $product = Product::where('team_id', $teamId)->findOrFail($id);
            $product->update($data);

            return $product;
        });
    }

    public function deleteForTeam(int $teamId, int $id): bool
    {
        return DB::transaction(function () use ($teamId, $id) {
            $product = Product::where('team_id', $teamId)->findOrFail($id);

            return $product->delete();
        });
    }
}
