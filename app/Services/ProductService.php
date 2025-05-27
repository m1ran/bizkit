<?php

namespace App\Services;

use App\Models\Product;
use App\Factories\RepositoryFactory;
use App\Contracts\EntityServiceInterface;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService implements EntityServiceInterface
{
    private int $teamId;
    private ProductRepository $repo;

    /**
     * Create a new class entity.
     */
    public function __construct(RepositoryFactory $factory)
    {
        $this->repo = $factory->make('product');
        $this->teamId = auth()->user()->current_team_id;
    }

    public function find(int $id): Product
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

    public function create(array $data): Product
    {
        return $this->repo->createForTeam($this->teamId, $data);
    }

    public function update(int $id, array $data): Product
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
