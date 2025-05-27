<?php

namespace App\Services;

use App\Models\Order;
use App\Factories\RepositoryFactory;
use App\Contracts\EntityServiceInterface;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class OrderService implements EntityServiceInterface
{
    private int $teamId;
    private OrderRepository $repo;

    /**
     * Create a new class entity.
     */
    public function __construct(RepositoryFactory $factory)
    {
        $this->repo = $factory->make('order');
        $this->teamId = auth()->user()->current_team_id;
    }

    public function find(int $id): Order
    {
        return $this->repo->findByTeam($this->teamId, $id);
    }

    public function list(array $filters, int $limit = 10): Collection
    {
        return $this->repo->getByTeam($this->teamId, $filters, $limit);
    }

    public function listPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->repo->getByTeamPaginated($this->teamId, $filters, $perPage);
    }

    public function create(array $data): Order
    {
        return $this->repo->createForTeam($this->teamId, $data);
    }

    public function update(int $id, array $data): Order
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
