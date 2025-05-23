<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'customer_id',
        'status_id',
        'total_cost',
        'total_price',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'status_id' => 'integer',
        'customer_id' => 'integer',
        'total_cost' => 'decimal:2',
        'total_price' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status()
    {
        return $this->belongsTo(OrderStatus::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
