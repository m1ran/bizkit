<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
