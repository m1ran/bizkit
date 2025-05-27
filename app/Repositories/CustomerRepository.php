<?php

namespace App\Repositories;

use App\Contracts\TeamScopedRepositoryInterface;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CustomerRepository implements TeamScopedRepositoryInterface
{
    /**
     * Get customers for a specific team with optional filters.
     *
     * @param int $teamId
     * @param array $filters
     * @param int $limit
     * @return Collection
     */
    public function getByTeam(int $teamId, array $filters = [], int $limit = 10): Collection
    {
        return Customer::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'address',
            ])
            ->orderBy('first_name')
            ->where('team_id', $teamId)
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $query->where('first_name', 'like', "%{$filters['q']}%")
                    ->orWhere('last_name', 'like', "%{$filters['q']}%")
                    ->orWhere('email', 'like', "%{$filters['q']}%")
                    ->orWhere('phone', 'like', "%{$filters['q']}%");
            })
            ->get();
    }

    /**
     * Get paginated customers for a specific team with optional filters.
     *
     * @param int $teamId
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByTeamPaginated(int $teamId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return Customer::query()
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'phone',
                'notes',
            ])
            ->latest()
            ->where('team_id', $teamId)
            ->when(!empty($filters['q']), function ($query) use ($filters) {
                $q = $filters['q'];
                $query->where(function ($query) use ($q) {
                    $query->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('phone', 'like', "%{$q}%")
                        ->orWhere('notes', 'like', "%{$q}%");
                });
            })
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    /**
     * Find a customer by ID for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return Customer
     */
    public function findByTeam(int $teamId, int $id): Customer
    {
        return Customer::where('team_id', $teamId)->find($id);
    }

    /**
     * Create a new customer for a specific team.
     *
     * @param int $teamId
     * @param array $data
     * @return Customer
     */
    public function createForTeam(int $teamId, array $data): Customer
    {
        return Customer::create(array_merge($data, ['team_id' => $teamId]));
    }

    /**
     * Update an existing customer for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @param array $data
     * @return Customer
     */
    public function updateForTeam(int $teamId, int $id, array $data): Customer
    {
        $customer = Customer::where('team_id', $teamId)->findOrFail($id);
        $customer->update($data);

        return $customer;
    }

    /**
     * Delete a customer for a specific team.
     *
     * @param int $teamId
     * @param int $id
     * @return bool
     */
    public function deleteForTeam(int $teamId, int $id): bool
    {
        $customer = Customer::where('team_id', $teamId)->findOrFail($id);

        return $customer->delete();
    }
}
