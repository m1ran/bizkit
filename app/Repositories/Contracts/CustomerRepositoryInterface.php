<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CustomerRepositoryInterface
{
    public function getByTeamPaginated(int $teamId, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function createForTeam(int $teamId, array $data): Customer;

    public function updateForTeam(int $teamId, int $id, array $data): Customer;

    public function deleteForTeam(int $teamId, int $id): bool;
}
