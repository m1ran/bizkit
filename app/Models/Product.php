<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
        'sku',
        'description',
        'cost',
        'price',
        'quantity',
        'category_id',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'cost' => 'decimal:2',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $auditable = [
        'name',
        'sku',
        'cost',
        'price',
        'quantity',
        'description',
        'category_id',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class);
    }
}
