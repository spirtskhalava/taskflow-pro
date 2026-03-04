<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProjectRepository extends BaseRepository implements ProjectRepositoryInterface
{
    public function __construct(Project $model)
    {
        parent::__construct($model);
    }

    public function getAllForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['owner', 'members'])
            ->withCount(['tasks', 'tasks as completed_tasks_count' => fn ($q) => $q->where('status', 'done')])
            ->forUser($user)
            ->when(isset($filters['archived']), fn ($q) => $q->where('is_archived', $filters['archived']))
            ->when(!isset($filters['archived']), fn ($q) => $q->active())
            ->when(isset($filters['search']), fn ($q) => $q->where('name', 'like', "%{$filters['search']}%"))
            ->latest()
            ->paginate($filters['per_page'] ?? 15);
    }

    public function findById(int $id): Project
    {
        return $this->model->newQuery()
            ->with(['owner', 'members', 'tasks.assignee'])
            ->findOrFail($id);
    }

    public function create(array $data): Project
    {
        $project = $this->model->newQuery()->create($data);

        // Owner is automatically a member with Owner role
        $project->members()->attach($data['owner_id'], ['role' => 'owner']);

        return $project->load(['owner', 'members']);
    }

    public function update(Project $project, array $data): Project
    {
        $project->update($data);
        return $project->fresh(['owner', 'members']);
    }

    public function delete(Project $project): bool
    {
        return $project->delete();
    }

    public function archive(Project $project): Project
    {
        $project->update(['is_archived' => true]);
        return $project->fresh();
    }

    public function restore(Project $project): Project
    {
        $project->update(['is_archived' => false]);
        return $project->fresh();
    }
}
