<?php

namespace App\Http\Controllers\Api;

use App\Factories\EntityServiceFactory;
use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;

class CustomerApiController extends Controller
{
    private CustomerService $service;

    public function __construct(EntityServiceFactory $factory)
    {
        $this->service = $factory->make('customer');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = request()->only(['q']);
        $customers = $this->service->list($filters);

        return response()->json(CustomerResource::collection($customers));
    }
}
