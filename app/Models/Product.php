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
        'price',
        'quantity',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'price' => 'decimal:2',
        'quantity' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $auditable = [
        'name',
        'sku',
        'price',
        'quantity',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
