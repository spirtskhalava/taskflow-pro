<?php

namespace App\Repositories;

use App\Enums\TaskStatus;
use App\Models\Project;
use App\Models\Task;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TaskRepository extends BaseRepository implements TaskRepositoryInterface
{
    public function __construct(Task $model)
    {
        parent::__construct($model);
    }

    public function getForProject(Project $project, array $filters = []): Collection
    {
        return $project->tasks()
            ->with(['assignee', 'reporter', 'comments.user', 'attachments'])
            ->withCount('comments')
            ->when(isset($filters['status']), fn ($q) => $q->byStatus($filters['status']))
            ->when(isset($filters['priority']), fn ($q) => $q->byPriority($filters['priority']))
            ->when(isset($filters['assignee_id']), fn ($q) => $q->assignedTo($filters['assignee_id']))
            ->when(isset($filters['search']), fn ($q) => $q->where('title', 'like', "%{$filters['search']}%"))
            ->orderBy('position')
            ->orderBy('created_at')
            ->get();
    }

    public function findById(int $id): Task
    {
        return $this->model->newQuery()
            ->with(['project', 'assignee', 'reporter', 'comments.user', 'attachments'])
            ->withCount('comments')
            ->findOrFail($id);
    }

    public function create(array $data): Task
    {
        // Place new task at end of the column
        $maxPosition = $this->model->newQuery()
            ->where('project_id', $data['project_id'])
            ->where('status', $data['status'] ?? 'todo')
            ->max('position') ?? 0;

        $data['position'] = $maxPosition + 1;

        return $this->model->newQuery()
            ->create($data)
            ->load(['assignee', 'reporter']);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update($data);
        return $task->fresh(['assignee', 'reporter', 'comments.user']);
    }

    public function delete(Task $task): bool
    {
        return $task->delete();
    }

    public function updateStatus(Task $task, string $status): Task
    {
        $completedAt = $status === TaskStatus::Done->value ? now() : null;

        $task->update([
            'status'       => $status,
            'completed_at' => $completedAt,
        ]);

        return $task->fresh(['assignee']);
    }

    public function reorder(Project $project, array $orderedIds): void
    {
        DB::transaction(function () use ($orderedIds) {
            foreach ($orderedIds as $position => $taskId) {
                $this->model->newQuery()
                    ->where('id', $taskId)
                    ->update(['position' => $position + 1]);
            }
        });
    }
}
