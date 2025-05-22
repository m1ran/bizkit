<?php

namespace App\Repositories;

use App\Contracts\TeamScopedRepositoryInterface;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductRepository implements TeamScopedRepositoryInterface
{
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
                $query->where('name', 'like', "%{$filters['q']}%")
                    ->orWhere('sku', 'like', "%{$filters['q']}%");
            })
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    public function findByTeam(int $teamId, int $id): Product
    {
        return Product::where('team_id', $teamId)->findOrFail($id);
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
