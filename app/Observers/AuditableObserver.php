<?php

namespace App\Observers;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditableObserver
{
    public function created(Model $m)
    {
        $this->createAudit($m, 'created');
    }

    public function updated(Model $m)
    {
        $this->createAudit($m, 'updated');
    }

    public function deleted(Model $m)
    {
        $this->createAudit($m, 'deleted');
    }

    private function createAudit(Model $m, string $event)
    {
        $data = ($event === 'deleted')
            ? $m->getOriginal()
            : $m->only($m->getAuditableAttributes());

        Audit::create([
            'user_id' => Auth::id(),
            'auditable_id' => $m->getKey(),
            'auditable_type' => $m->getMorphClass(),
            'event' => $event,
            'data' => $data,
        ]);
    }
}
