<?php

namespace App\Repositories;

use App\Contracts\TeamScopedRepositoryInterface;
use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CustomerRepository implements TeamScopedRepositoryInterface
{
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
                $query->where('first_name', 'like', "%{$filters['q']}%")
                    ->orWhere('last_name', 'like', "%{$filters['q']}%")
                    ->orWhere('email', 'like', "%{$filters['q']}%")
                    ->orWhere('phone', 'like', "%{$filters['q']}%")
                    ->orWhere('notes', 'like', "%{$filters['q']}%");
            })
            ->paginate($perPage)
            ->withQueryString()
            ->onEachSide(1);
    }

    public function findByTeam(int $teamId, int $id): Customer
    {
        return Customer::where('team_id', $teamId)->findOrFail($id);
    }

    public function createForTeam(int $teamId, array $data): Customer
    {
        return DB::transaction(function () use ($teamId, $data) {
            return Customer::create(array_merge($data, ['team_id' => $teamId]));
        });
    }

    public function updateForTeam(int $teamId, int $id, array $data): Customer
    {
        return DB::transaction(function () use ($teamId, $id, $data) {
            $customer = Customer::where('team_id', $teamId)->findOrFail($id);
            $customer->update($data);

            return $customer;
        });
    }

    public function deleteForTeam(int $teamId, int $id): bool
    {
        return DB::transaction(function () use ($teamId, $id) {
            $customer = Customer::where('team_id', $teamId)->findOrFail($id);

            return $customer->delete();
        });
    }
}
