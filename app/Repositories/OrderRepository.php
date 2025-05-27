<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\TeamScopedRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OrderRepository implements TeamScopedRepositoryInterface
{
    /**
     * Get orders for a specific team with optional filters.
     *
     * @param int $teamId
     * @param array $filters
     * @param int $limit
     * @return Collection
     */
    public function getByTeam(int $teamId, array $filters = [], $limit = 10): Collection
    {
        return Order::query()
            ->select([
                'id',
                'order_number',
                'customer_id',
                'status_id',
                'total_cost',
                'total_price',
            ])
            ->orderBy('order_number')
            ->where('team_id', $teamId)
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $query->where('order_number', 'like', "%{$filters['q']}%");
            })
            ->get();
    }

    /**
     * Get paginated orders for a specific team with optional filters.
     *
     * @param int $teamId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByTeamPaginated(int $teamId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Order::query()
            ->select([
                'id',
                'order_number',
                'customer_id',
                'status_id',
                'total_cost',
                'total_price',
                'notes',
                'created_at',
            ])
            ->latest()
            ->where('team_id', $teamId)
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $query->where('order_number', 'like', "%{$filters['q']}%");
            })
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    /**
     * Find an order by its ID for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return Order
     */
    public function findByTeam(int $teamId, int $id): Order
    {
        return Order::where('team_id', $teamId)->find($id);
    }

    /**
     * Create a new order for a specific team.
     *
     * @param int $teamId
     * @param array $data
     * @return Order
     */
    public function createForTeam(int $teamId, array $data): Order
    {
        return Order::create(array_merge($data, ['team_id' => $teamId]));
    }

    /**
     * Update an existing order for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @param array $data
     * @return Order
     */
    public function updateForTeam(int $teamId, int $id, array $data): Order
    {
        $order = Order::where('team_id', $teamId)->findOrFail($id);
        $order->update($data);

        return $order;
    }

    /**
     * Delete an order for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return bool
     */
    public function deleteForTeam(int $teamId, int $id): bool
    {
        $order = Order::where('team_id', $teamId)->findOrFail($id);

        return $order->delete();
    }
}
