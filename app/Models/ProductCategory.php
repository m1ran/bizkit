<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'name',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
