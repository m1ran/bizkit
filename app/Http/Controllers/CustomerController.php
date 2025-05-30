<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use App\Factories\EntityServiceFactory;
use App\Traits\HandleCrudActions;

class CustomerController extends Controller
{
    use HandleCrudActions;

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

        $customers = $this->service->listPaginated($filters);

        return Inertia::render('Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'filters' => $filters,
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerRequest $request)
    {
        return $this->handleAction(
            fn () => $this->service->create($request->validated()),
            __('Customer created successfully.'),
            __('Check for correct customer data.'),
            'customers.index'
        );
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(CustomerRequest $request, string $id)
    {
        return $this->handleAction(
            fn () => $this->service->update($id, $request->validated()),
            __('Customer updated successfully.'),
            __('Check for correct customer data.'),
            'customers.index'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->handleAction(
            fn () => $this->service->delete($id),
            __('Customer deleted successfully.'),
            __('Check for correct customer data.'),
            'customers.index'
        );
    }
}
