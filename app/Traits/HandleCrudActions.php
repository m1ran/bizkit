<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;
use Throwable;

trait HandleCrudActions
{
    protected function handleAction(
        callable $action,
        string $successMsg,
        string $errorMsg,
        string $redirectRoute
    )
    {
        $flashData = [
            'flash.banner' => $successMsg,
            'flash.bannerStyle' => 'success',
        ];

        try {
            $action();
        } catch (Throwable $e) {
            Log::error(static::class . ': ' . $e->getMessage());

            $flashData['flash.banner'] = $errorMsg;
            $flashData['flash.bannerStyle'] = 'danger';
        }

        return redirect()->route($redirectRoute)->with($flashData);
    }
}
