<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Relations\MorphMany;

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

    public function audits(): MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable');
    }

    public function getAudits()
    {
        return $this->audits()
            ->with('user')
            ->orderByDesc('created_at')
            ->get();
    }
}
