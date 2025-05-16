<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerObserver
{
    /**
     * Handle the Customer "creating" event.
     */
    public function creating(Customer $customer): void
    {
        if (Auth::check()) {
            $customer->created_by = Auth::id();
            $customer->updated_by = Auth::id();
        }
    }

    /**
     * Handle the Customer "updating" event.
     */
    public function updating(Customer $customer): void
    {
        if (Auth::check()) {
            $customer->updated_by = Auth::id();
        }
    }
}
