<?php

namespace App\Services;

use App\Models\Customer;
use App\Factories\RepositoryFactory;
use App\Repositories\CustomerRepository;
use App\Contracts\EntityServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CustomerService implements EntityServiceInterface
{
    private int $teamId;
    private CustomerRepository $repo;

    /**
     * Create a new class entity.
     */
    public function __construct(RepositoryFactory $factory)
    {
        $this->repo = $factory->make('customer');
        $this->teamId = Auth::user()->current_team_id;
    }

    /**
     * Find a customer by its ID for the current team.
     *
     * @param int $id
     * @param array $relations
     * @return Customer
     */
    public function find(int $id, array $relations = []): Customer
    {
        return $this->repo->findByTeam($this->teamId, $id, $relations);
    }

    /**
     * List customers for the current team with optional filters.
     *
     * @param array $filters
     * @param int $limit
     * @return Collection
     */
    public function list(array $filters, int $limit = 10): Collection
    {
        return $this->repo->getByTeam($this->teamId, $filters, $limit);
    }

    /**
     * List paginated customers for the current team with optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->getByTeamPaginated($this->teamId, $filters, $perPage);
    }

    /**
     * Get all customers for the current team.
     *
     * @return Collection
     */
    public function create(array $data): Customer
    {
        return DB::transaction(function() use ($data) {
            return $this->repo->createForTeam($this->teamId, $data);
        });
    }

    /**
     * Update a customer for the current team.
     *
     * @param int $id
     * @param array $data
     * @return Customer
     */
    public function update(int $id, array $data): Customer
    {
        return DB::transaction(function() use ($id, $data) {
            return $this->repo->updateForTeam($this->teamId, $id, $data);
        });
    }

    /**
     * Delete a customer for the current team.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function() use ($id) {
            return $this->repo->deleteForTeam($this->teamId, $id);
        });
    }

    /**
     * Get the history of changes for a specific customer.
     *
     * @param int $id
     * @return array
     */
    public function history(int $id): array
    {
        return $this->repo
            ->findByTeam($this->teamId, $id)
            ->getAudits()
            ->toArray();
    }
}
