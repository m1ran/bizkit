<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TeamScopedRepositoryInterface
{
    public function getByTeam(int $teamId, array $filters = [], int $limit): Collection;

    public function getByTeamPaginated(int $teamId, array $filters = [], int $perPage): LengthAwarePaginator;

    public function findByTeam(int $teamId, int $id): Model;

    public function createForTeam(int $teamId, array $data): Model;

    public function updateForTeam(int $teamId, int $id, array $data): Model;

    public function deleteForTeam(int $teamId, int $id): bool;
}
