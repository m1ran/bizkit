<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface EntityServiceInterface
{
    public function find(int $id, array $relations = []): Model;

    public function list(array $filters, int $limit): Collection;

    public function listPaginated(array $filters, int $perPage): LengthAwarePaginator;

    public function create(array $data): Model;

    public function update(int $id, array $data): Model;

    public function delete(int $id): bool;

    public function history(int $id): array;
}
