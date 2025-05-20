<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use Auditable;
    use SoftDeletes;

    protected $fillable = [
        'team_id',
        'first_name',
        'last_name',
        'patronymic_name',
        'email',
        'phone',
        'address',
        'city',
        'state_id',
        'region_id',
        'zip',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'team_id' => 'integer',
        'state_id' => 'integer',
        'region_id' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $auditable = [
        'first_name',
        'last_name',
        'email',
        'phone',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }
}
