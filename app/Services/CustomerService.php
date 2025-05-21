<?php

namespace App\Services;

use App\Models\Customer;
use App\Factories\RepositoryFactory;
use App\Repositories\CustomerRepository;
use App\Contracts\EntityServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
        $this->teamId = auth()->user()->current_team_id;
    }

    public function list(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->getByTeamPaginated($this->teamId, $filters, $perPage);
    }

    public function create(array $data): Customer
    {
        return $this->repo->createForTeam($this->teamId, $data);
    }

    public function update(int $id, array $data): Customer
    {
        return $this->repo->updateForTeam($this->teamId, $id, $data);
    }

    public function delete(int $id): bool
    {
        return $this->repo->deleteForTeam($this->teamId, $id);
    }

    public function history(int $id): array
    {
        return $this->repo
            ->findByTeam($this->teamId, $id)
            ->getAudits()
            ->toArray();
    }
}
