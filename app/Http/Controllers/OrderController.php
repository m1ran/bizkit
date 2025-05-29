<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Factories\EntityServiceFactory;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderStatusResource;
use App\Services\OrderService;
use App\Traits\HandleCrudActions;

class OrderController extends Controller
{
    use HandleCrudActions;

    private OrderService $service;

    public function __construct(EntityServiceFactory $factory)
    {
        $this->service = $factory->make('order');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = request()->only(['q']);

        $products = $this->service->listPaginated($filters);
        $statuses = $this->service->getStatuses();

        return Inertia::render('Orders/Index', [
            'filters' => $filters,
            'orders' => OrderResource::collection($products),
            'statuses' => OrderStatusResource::collection($statuses),
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        return $this->handleAction(
            fn () => $this->service->create($request->validated()),
            __('Order created successfully.'),
            __('Check for correct order data.'),
            'orders.index'
        );
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(UpdateOrderRequest $request, string $id)
    {
        return $this->handleAction(
            fn () => $this->service->update($id, $request->validated()),
            __('Order updated successfully.'),
            __('Check for correct order data.'),
            'orders.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->handleAction(
            fn () => $this->service->delete($id),
            __('Order deleted successfully.'),
            __('Check for order product data.'),
            'orders.index'
        );
    }
}
