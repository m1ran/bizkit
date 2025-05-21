<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait Auditable
{
    /**
     * If model has list of auditable attributes, take It
     */
    public function getAuditableAttributes(): array
    {
        return property_exists($this, 'auditable') && is_array($this->auditable) && count($this->auditable)
            ? $this->auditable
            : array_keys($this->getAttributes());
    }

    public function audits(): HasMany
    {
        return $this->hasMany(Audit::class, 'auditable_id')
            ->where('auditable_entity', class_basename($this));
    }

    public function getAudits()
    {
        return $this->audits()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }
}
