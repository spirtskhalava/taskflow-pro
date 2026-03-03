<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProjectRepositoryInterface
{
    public function getAllForUser(User $user, array $filters = []): LengthAwarePaginator;

    public function findById(int $id): Project;

    public function create(array $data): Project;

    public function update(Project $project, array $data): Project;

    public function delete(Project $project): bool;

    public function archive(Project $project): Project;

    public function restore(Project $project): Project;
}
