<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Repositories\Contracts\CustomerRepositoryInterface;
use Exception;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CustomerRepositoryInterface $repository)
    {
        $filters = request()->only(['q']);

        $customers = $repository->getByTeamPaginated(auth()->user()->current_team_id, $filters);

        return Inertia::render('Customers/Index', [
            'customers' => CustomerResource::collection($customers),
            'filters' => request()->only(['q']),
        ]);
    }

    /**
     * Store a newly created customer in storage.
     */
    public function store(CustomerRequest $request, CustomerRepositoryInterface $repository)
    {
        $data = $request->validated();

        $repository->createForTeam(auth()->user()->current_team_id, $data);

        return redirect()->route('customers.index')->with([
            'flash.banner' => __('Customer created successfully.'),
            'flash.bannerStyle' => 'success',
        ]);
    }

    /**
     * Update the specified customer in storage.
     */
    public function update(CustomerRequest $request, CustomerRepositoryInterface $repository, string $id)
    {
        $data = $request->validated();

        try {
            $repository->updateForTeam(auth()->user()->current_team_id, $id, $data);
        } catch (Exception $e) {
            return redirect()->route('customers.index')->with([
                'flash.banner' => __('Check for correct customer data.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        return redirect()->route('customers.index')->with([
            'flash.banner' => __('Customer updated successfully.'),
            'flash.bannerStyle' => 'success',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CustomerRepositoryInterface $repository, string $id)
    {
        try {
            $repository->deleteForTeam(auth()->user()->current_team_id, $id);
        } catch (Exception $e) {
            return redirect()->route('customers.index')->with([
                'flash.banner' => __('Check for correct customer data.'),
                'flash.bannerStyle' => 'danger',
            ]);
        }

        return redirect()->route('customers.index')->with([
            'flash.banner' => __('Customer deleted successfully.'),
            'flash.bannerStyle' => 'success',
        ]);
    }
}
