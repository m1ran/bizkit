<?php

namespace App\Services;

use App\Models\Order;
use App\Factories\RepositoryFactory;
use App\Contracts\EntityServiceInterface;
use App\Models\OrderStatus;
use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderService implements EntityServiceInterface
{
    const ORDER_DEFAULT_STATUS = 'draft';
    const ORDER_FINISHED_STATUS = 'finished';

    private int $teamId;
    private OrderRepository $orderRepo;
    private ProductRepository $productRepo;
    private CustomerRepository $customerRepo;

    /**
     * Create a new class entity.
     */
    public function __construct(RepositoryFactory $factory)
    {
        $this->orderRepo = $factory->make('order');
        $this->productRepo = $factory->make('product');
        $this->customerRepo = $factory->make('customer');
        $this->teamId = Auth::user()->current_team_id;
    }

    /**
     * Find an order by its ID for the current team.
     *
     * @param int $id
     * @param array $relations
     * @return Order
     */
    public function find(int $id, array $relations = []): Order
    {
        return $this->orderRepo->findByTeam($this->teamId, $id, $relations);
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
            $customerId = $this->handleCustomerAction($data);
            // Prepare order data
            $orderData = $this->prepareOrderData($data, $customerId);
            // Create the order
            $order = $this->orderRepo->createForTeam($this->teamId, $orderData);
            // Handle order items
            $this->handleOrderItems($order, $data['items']);

            return $order;
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
            $customerId = $this->handleCustomerAction($data);
            // Prepare order data
            $orderData = $this->prepareOrderData($data, $customerId);
            // Update the order
            $order = $this->orderRepo->updateForTeam($this->teamId, $id, $orderData);
            // Handle order items
            $this->handleOrderItems($order, $data['items']);

            return $order;
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

    /**
     * Handles the creation or update of a customer based on the provided data.
     *
     * @param array $data
     * @return int
     */
    private function handleCustomerAction(array $data): int
    {
        if (empty($data['customer_id'])) {
            return $this->createCustomer($data);
        }

        $customerAction = $data['customer_action'] ?? 'keep';

        switch ($customerAction) {
            case 'create':
                return $this->createCustomer($data);
            case 'update':
                return $this->updateCustomer($data);
            case 'keep':
            default:
                return (int) $data['customer_id'];
        }
    }

    /**
     * Handles the customer creation or update based on the provided data.
     *
     * @param array $data
     * @return int|null
     */
    private function createCustomer(array $data): int
    {
        $customerData = $this->extractCustomerData($data);

        $customer = $this->customerRepo->createForTeam($this->teamId, $customerData);

        return $customer->id;
    }

    /**
     * Updates an existing customer for the current team.
     *
     * @param array $data
     * @return int
     */
    private function updateCustomer(array $data): int
    {
        $customerId = $data['customer_id'];

        if (!$customerId) {
            throw new \InvalidArgumentException('Customer ID is required for update action');
        }

        $customerData = $this->extractCustomerData($data);

        $this->customerRepo->updateForTeam($this->teamId, $customerId, $customerData);

        return $customerId;
    }

    /**
     * Extracts customer data from the provided array.
     *
     * @param array $data
     * @return array
     */
    private function extractCustomerData(array $data): array
    {
        return [
            'first_name' => $data['first_name'] ?? '',
            'last_name'  => $data['last_name'] ?? '',
            'phone'      => $data['phone'] ?? '',
            'address'    => $data['address'] ?? '',
        ];
    }

    /**
     * Prepares the order data for creation or update.
     *
     * @param array $data
     * @param int|null $customerId
     * @return array
     */
    private function prepareOrderData(array $data, ?int $customerId): array
    {
        $orderData = [
            'customer_id' => $customerId,
            'notes'       => $data['notes'] ?? '',
            'first_name'  => $data['first_name'] ?? '',
            'last_name'   => $data['last_name'] ?? '',
            'phone'       => $data['phone'] ?? '',
            'address'     => $data['address'] ?? '',
        ];
        // Add other order-specific fields
        if (isset($data['num'])) {
            $orderData['num'] = $data['num'];
        } else {
            $orderData['num'] = $this->orderRepo->getNextOrderNumber($this->teamId);
        }
        // set the status_id based on the provided data or default to the team's default status
        if (isset($data['status_id'])) {
            $orderData['status_id'] = $data['status_id'];
        } else {
            if ($data['finished'] ?? false) {
                $orderData['status_id'] = $this->getFinishedStatusId($this->teamId);
            } else {
                $orderData['status_id'] = $this->getDefaultStatusId($this->teamId);
            }
        }

        return $orderData;
    }

    /**
     * Handles the order items for the given order.
     *
     * @param Order $order
     * @param array $items
     * @return void
     */
    private function handleOrderItems(Order $order, array $items): void
    {
        // Get current order items indexed by product_id
        $existingItems = $order->items()->get()->keyBy('product_id');
        $newItems = collect($items)->keyBy('product_id');

        $totalCost = 0;
        $totalPrice = 0;
        // Iterate over new items
        foreach ($newItems as $productId => $item) {
            $product = $this->productRepo->findByTeam($this->teamId, $productId);

            $existingItem = $existingItems->get($productId);

            $quantity = $item['quantity'];
            $unitCost = $product->cost ?? 0;
            $unitPrice = $product->price ?? 0;
            $lineCost = $quantity * $unitCost;
            $linePrice = $quantity * $unitPrice;

            if ($existingItem) {
                // If quantity or price changed, update the item
                $quantityDiff = $quantity - $existingItem->quantity;
                // Adjust stock based on quantity difference
                if ($quantityDiff > 0) {
                    $product->decrement('quantity', $quantityDiff);
                } else if ($quantityDiff < 0) {
                    $product->increment('quantity', abs($quantityDiff));
                }

                if (
                    $existingItem->quantity !== $quantity ||
                    $existingItem->unit_cost != $unitCost ||
                    $existingItem->unit_price != $unitPrice
                ) {
                    $existingItem->update([
                        'quantity' => $quantity,
                        'unit_cost' => $unitCost,
                        'unit_price' => $unitPrice,
                    ]);
                }
            } else {
                // If item is new, create it and decrement stock
                $product->decrement('quantity', $quantity);

                $order->items()->create([
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'unit_price' => $unitPrice,
                ]);
            }

            $totalCost += $lineCost;
            $totalPrice += $linePrice;
        }

        // Iterate over old items and delete those not present in new items
        foreach ($existingItems as $productId => $existingItem) {
            if (!$newItems->has($productId)) {
                // Return stock to inventory
                $existingItem->product->increment('quantity', $existingItem->quantity);
                $existingItem->delete();
            }
        }
        // Update order totals
        $order->update([
            'total_cost' => $totalCost,
            'total_price' => $totalPrice,
        ]);
    }

    /**
     * Get the list of order statuses.
     *
     * @return Collection
     */
    public function getStatuses(): Collection
    {
        return OrderStatus::select(['id', 'name', 'label', 'sort_order'])
            ->orderBy('sort_order')
            ->get();
    }

    /**
     * Get the default status ID for orders.
     *
     * @return int
     */
    public function getDefaultStatusId(): int
    {
        return OrderStatus::where('name', self::ORDER_DEFAULT_STATUS)->value('id') ?? 0;
    }

    /**
     * Get the finished status ID for orders.
     *
     * @return int
     */
    public function getFinishedStatusId(): int
    {
        return OrderStatus::where('name', self::ORDER_FINISHED_STATUS)->value('id') ?? 0;
    }
}
