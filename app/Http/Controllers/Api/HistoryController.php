<?php

namespace App\Http\Controllers\Api;

use App\Factories\RepositoryFactory;
use App\Factories\ServiceFactory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Throwable;

class HistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function show(string $entity, int $id, ServiceFactory $factory)
    {
        try {
            $service = $factory->make($entity);
            // Get instance history records
            $history = $service->history($id);

            return response()->json(compact('id', 'entity', 'history'));
        } catch (Throwable $e) {
            Log::error("API HistoryController@show error for entity [{$entity}] id [{$id}]: {$e->getMessage()}", [
                'exception' => $e,
                'entity'    => $entity,
                'id'        => $id,
            ]);

            return response()->json([
                'error'   => 'Internal Server Error',
                'message' => 'Something went wrong while fetching entity history.',
            ], 500);
        }
    }
}
