<?php

namespace App\Http\Controllers\Api;

use App\Factories\EntityServiceFactory;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;

class ProductApiController extends Controller
{
    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = request()->only(['q']);

        $products = $this->service->list($filters);

        return response()->json(ProductResource::collection($products));
    }
}
