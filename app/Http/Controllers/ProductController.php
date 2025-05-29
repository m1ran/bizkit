<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\ProductRequest;
use App\Factories\EntityServiceFactory;
use App\Http\Resources\ProductCategoryResource;
use App\Http\Resources\ProductResource;
use App\Services\ProductService;
use App\Traits\HandleCrudActions;

class ProductController extends Controller
{
    use HandleCrudActions;

    private ProductService $service;

    public function __construct(EntityServiceFactory $factory)
    {
        $this->service = $factory->make('product');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $filters = request()->only(['q']);

        $categories = $this->service->listCategories();
        $products = $this->service->listPaginated($filters);

        return Inertia::render('Products/Index', [
            'filters'    => $filters,
            'products'   => ProductResource::collection($products),
            'categories' => ProductCategoryResource::collection($categories),
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(ProductRequest $request)
    {
        return $this->handleAction(
            fn () => $this->service->create($request->validated()),
            __('Product created successfully.'),
            __('Check for correct product data.'),
            'products.index'
        );
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(ProductRequest $request, string $id)
    {
        return $this->handleAction(
            fn () => $this->service->update($id, $request->validated()),
            __('Product updated successfully.'),
            __('Check for correct product data.'),
            'products.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->handleAction(
            fn () => $this->service->delete($id),
            __('Product deleted successfully.'),
            __('Check for correct product data.'),
            'products.index'
        );
    }
}
