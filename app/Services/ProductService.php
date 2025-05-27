<?php

namespace App\Services;

use App\Models\Product;
use App\Factories\RepositoryFactory;
use App\Contracts\EntityServiceInterface;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

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

    /**
     * Find a product by its ID for the current team.
     *
     * @param int $id
     * @return Product
     */
    public function find(int $id): Product
    {
        return $this->repo->findByTeam($this->teamId, $id);
    }

    /**
     * List products for the current team with optional filters.
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
     * List paginated products for the current team with optional filters.
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
     * Create a new product for the current team.
     *
     * @param array $data
     * @return Product
     */
    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            return $this->repo->createForTeam($this->teamId, $data);
        });
    }

    /**
     * Update an existing product for the current team.
     *
     * @param int $id
     * @param array $data
     * @return Product
     */
    public function update(int $id, array $data): Product
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->repo->updateForTeam($this->teamId, $id, $data);
        });
    }

    /**
     * Delete a product for the current team.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->repo->deleteForTeam($this->teamId, $id);
        });
    }

    /**
     * Get the history of changes for a specific product.
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
