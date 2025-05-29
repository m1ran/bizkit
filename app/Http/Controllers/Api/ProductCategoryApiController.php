<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use App\Services\ProductService;
use App\Traits\ApiResponse;

class ProductCategoryApiController extends Controller
{
    use ApiResponse;

    private ProductService $service;

    public function __construct(ProductService $service)
    {
        $this->service = $service;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductCategoryRequest $request)
    {
        try {
            $category = $this->service->createCategory($request->validated());

            return $this->successResponse($category->only(['id', 'name']), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductCategoryRequest $request, string $id)
    {
        try {
            $category = $this->service->updateCategory($id, $request->validated());

            return $this->successResponse($category->only(['id', 'name']), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            return $this->successResponse($this->service->deleteCategory($id), 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage());
        }
    }
}
