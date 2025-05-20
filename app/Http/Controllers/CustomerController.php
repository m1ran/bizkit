<?php

namespace App\Http\Controllers;

use App\Factories\RepositoryFactory;
use App\Factories\ServiceFactory;
use Inertia\Inertia;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Services\CustomerService;
use Exception;

class CustomerController extends Controller
{
    private CustomerService $service;

    public function __construct(ServiceFactory $factory)
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

        return Inertia::render('Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'filters' => request()->only(['q']),
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()->route('customers.index')->with([
            'flash.banner' => __('Customer created successfully.'),
            'flash.bannerStyle' => 'success',
        ]);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(CustomerRequest $request, string $id)
    {
        $flashData = [
            'flash.banner' => __('Customer updated successfully.'),
            'flash.bannerStyle' => 'success',
        ];

        try {
            $this->service->update($id, $request->validated());
        } catch (Exception $e) {
            $flashData = [
                'flash.banner' => __('Check for correct customer data.'),
                'flash.bannerStyle' => 'danger',
            ];
        }

        return redirect()->route('customers.index')->with($flashData);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $flashData = [
            'flash.banner' => __('Customer deleted successfully.'),
            'flash.bannerStyle' => 'success',
        ];

        try {
            $this->service->delete($id);
        } catch (Exception $e) {
            $flashData = [
                'flash.banner' => __('Check for correct customer data.'),
                'flash.bannerStyle' => 'danger',
            ];
        }

        return redirect()->route('customers.index')->with($flashData);
    }
}
