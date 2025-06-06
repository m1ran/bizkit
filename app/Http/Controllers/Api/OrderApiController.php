<?php

namespace App\Http\Controllers\Api;

use App\Factories\EntityServiceFactory;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderApiController extends Controller
{
    private OrderService $service;

    public function __construct(EntityServiceFactory $factory)
    {
        $this->service = $factory->make('order');
    }

    /**
     * Display the specified resource.
     */
    public function view(int $id)
    {
        $order = $this->service->find($id, ['items', 'customer' , 'items.product']);

        return response()->json(new OrderResource($order));
    }
}
