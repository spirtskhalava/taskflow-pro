<?php

namespace App\Services;

use App\Events\TaskStatusChanged;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Repositories\Contracts\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

class TaskService
{
    public function __construct(
        private readonly TaskRepositoryInterface $taskRepository
    ) {
    }

    public function listForProject(Project $project, array $filters = []): Collection
    {
        return $this->taskRepository->getForProject($project, $filters);
    }

    public function create(Project $project, User $reporter, array $data): Task
    {
        $data['project_id'] = $project->id;
        $data['reporter_id'] = $reporter->id;

        return $this->taskRepository->create($data);
    }

    public function update(Task $task, array $data): Task
    {
        return $this->taskRepository->update($task, $data);
    }

    public function delete(Task $task): bool
    {
        return $this->taskRepository->delete($task);
    }

    public function updateStatus(Task $task, string $newStatus): Task
    {
        $oldStatus = $task->status->value;

        $updated = $this->taskRepository->updateStatus($task, $newStatus);

        event(new TaskStatusChanged($updated, $oldStatus, $newStatus));

        return $updated;
    }

    public function reorder(Project $project, array $orderedIds): void
    {
        $this->taskRepository->reorder($project, $orderedIds);
    }

    public function uploadAttachment(Task $task, \Illuminate\Http\UploadedFile $file): string
    {
        $path = $file->store("tasks/{$task->id}/attachments", 'public');

        $task->attachments()->create([
            'name'      => $file->getClientOriginalName(),
            'path'      => $path,
            'mime_type' => $file->getMimeType(),
            'size'      => $file->getSize(),
        ]);

        return Storage::url($path);
    }
}
