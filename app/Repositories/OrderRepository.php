<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Contracts\TeamScopedRepositoryInterface;
use App\Filters\OrderFilter;
use Illuminate\Database\Eloquent\Builder;

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
                'num',
                'status_id',
                'customer_id',
                'status_id',
                'total_cost',
                'total_price',
            ])
            ->where('team_id', $teamId)
            ->when(!empty($filters), fn($query) => $this->applyFilters($query, $filters))
            ->latest()
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
                'num',
                'status_id',
                'customer_id',
                'total_cost',
                'total_price',
                'first_name',
                'last_name',
                'phone',
                'address',
                'notes',
                'created_at',
                'updated_at',
            ])
            ->where('team_id', $teamId)
            ->when(!empty($filters), fn($query) => $this->applyFilters($query, $filters))
            ->latest()
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    /**
     * Find an order by its ID for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @param array $relations
     * @return Order
     */
    public function findByTeam(int $teamId, int $id, array $relations = []): Order
    {
        return Order::where('team_id', $teamId)
            ->when(!empty($relations), fn($query) => $query->with($relations))
            ->findOrFail($id);
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

    /**
     * Get the next order number for a specific team.
     *
     * The order number format is:
     * - Letter based on the year (A for 2025, B for 2026, etc.)
     * - Padded team ID (3 digits)
     * - Sequence number padded to 6 digits
     *
     * @param int $teamId
     * @return string
     */
    public function getNextOrderNumber(int $teamId): string
    {
        $year       = Carbon::now()->year;
        $yearOffset = $year - env('APP_LAUNCH_YEAR', 2025); // 0 - 2025, 1 - 2026, ...
        $letter     = chr(ord('A') + $yearOffset);
          // Prefix: letter + pad teamId to 3 digits
        $prefix = $letter . Str::padLeft($teamId, 3, '0');
          // Get max num for this team with the same prefix
        $lastNum = Order::withTrashed()
            ->where('team_id', $teamId)
            ->where('num', 'like', "$prefix%")
            ->max('num');
          // Get num after the prefix
        $lastSeq = $lastNum ? (int) substr($lastNum, 4) : 0;
        $nextSeq = $lastSeq + 1;
          // Pad the sequence number to 6 digits
        $seqPadded = Str::padLeft($nextSeq, 6, '0');

        return $prefix . $seqPadded;
    }

    /**
     * Apply filters to the query
     */
    public function applyFilters(Builder $query, array $filters): Builder
    {
        return (new OrderFilter($filters))->apply($query);
    }
}
