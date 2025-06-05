<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'num',
        'team_id',
        'customer_id',
        'status_id',
        'total_cost',
        'total_price',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'notes',
    ];

    protected $casts = [
        'team_id'     => 'integer',
        'status_id'   => 'integer',
        'customer_id' => 'integer',
        'total_cost'  => 'decimal:2',
        'total_price' => 'decimal:2',
        'deleted_at'  => 'datetime',
    ];

    protected $auditable = [
        'total_cost',
        'total_price',
        'notes',
        'status_id' => [
            'relation'  => 'status',
            'attribute' => 'label',
        ],
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
