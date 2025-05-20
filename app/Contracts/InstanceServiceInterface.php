<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface InstanceServiceInterface
{
    public function list(array $filters, int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;

    public function history(int $id): array;
}
