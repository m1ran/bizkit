<?php

namespace App\Services;

use App\Models\Order;
use App\Factories\RepositoryFactory;
use App\Contracts\EntityServiceInterface;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class OrderService implements EntityServiceInterface
{
    private int $teamId;
    private OrderRepository $orderRepo;
    private CustomerRepository $customerRepo;

    /**
     * Create a new class entity.
     */
    public function __construct(RepositoryFactory $factory)
    {
        $this->orderRepo = $factory->make('order');
        $this->customerRepo = $factory->make('customer');
        $this->teamId = auth()->user()->current_team_id;
    }

    /**
     * Find an order by its ID for the current team.
     *
     * @param int $id
     * @return Order
     */
    public function find(int $id): Order
    {
        return $this->orderRepo->findByTeam($this->teamId, $id);
    }

    /**
     * List orders for the current team with optional filters.
     *
     * @param array $filters
     * @param int $limit
     * @return Collection
     */
    public function list(array $filters, int $limit = 10): Collection
    {
        return $this->orderRepo->getByTeam($this->teamId, $filters, $limit);
    }


    /**
     * List paginated orders for the current team with optional filters.
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listPaginated(array $filters, int $perPage = 10): LengthAwarePaginator
    {
        return $this->orderRepo->getByTeamPaginated($this->teamId, $filters, $perPage);
    }

    /**
     * Create a new order for the current team.
     *
     * @param array $data
     * @return Order
     */
    public function create(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            return $this->orderRepo->createForTeam($this->teamId, $data);
        });
    }

    /**
     * Update an existing order for the current team.
     *
     * @param int $id
     * @param array $data
     * @return Order
     */
    public function update(int $id, array $data): Order
    {
        return DB::transaction(function () use ($id, $data) {
            return $this->orderRepo->updateForTeam($this->teamId, $id, $data);
        });
    }

    /**
     * Delete an order for the current team.
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            return $this->orderRepo->deleteForTeam($this->teamId, $id);
        });
    }

    /**
     * Get the history of changes for a specific order.
     *
     * @param int $id
     * @return array
     */
    public function history(int $id): array
    {
        return $this->orderRepo
            ->findByTeam($this->teamId, $id)
            ->getAudits()
            ->toArray();
    }
}
