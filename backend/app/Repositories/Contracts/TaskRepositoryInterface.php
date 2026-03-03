<?php

namespace App\Repositories\Contracts;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getForProject(Project $project, array $filters = []): Collection;

    public function findById(int $id): Task;

    public function create(array $data): Task;

    public function update(Task $task, array $data): Task;

    public function delete(Task $task): bool;

    public function updateStatus(Task $task, string $status): Task;

    public function reorder(Project $project, array $orderedIds): void;
}
