<?php

namespace App\Services;

use App\Models\Project;
use App\Models\User;
use App\Repositories\Contracts\ProjectRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProjectService
{
    public function __construct(
        private readonly ProjectRepositoryInterface $projectRepository
    ) {
    }

    public function listForUser(User $user, array $filters = []): LengthAwarePaginator
    {
        return $this->projectRepository->getAllForUser($user, $filters);
    }

    public function create(User $owner, array $data): Project
    {
        return DB::transaction(function () use ($owner, $data) {
            $data['owner_id'] = $owner->id;
            return $this->projectRepository->create($data);
        });
    }

    public function update(Project $project, array $data): Project
    {
        return $this->projectRepository->update($project, $data);
    }

    public function delete(Project $project): bool
    {
        return $this->projectRepository->delete($project);
    }

    public function archive(Project $project): Project
    {
        return $this->projectRepository->archive($project);
    }

    public function restore(Project $project): Project
    {
        return $this->projectRepository->restore($project);
    }

    public function inviteMember(Project $project, string $email, string $role): User
    {
        $user = User::where('email', $email)->firstOrFail();

        if ($project->hasMember($user)) {
            throw new \DomainException('User is already a member of this project.');
        }

        $project->members()->attach($user->id, ['role' => $role]);

        return $user;
    }

    public function removeMember(Project $project, User $user): void
    {
        if ($project->isOwnedBy($user)) {
            throw new \DomainException('Cannot remove the project owner.');
        }

        $project->members()->detach($user->id);
    }

    public function updateMemberRole(Project $project, User $user, string $role): void
    {
        if ($project->isOwnedBy($user) && $role !== 'owner') {
            throw new \DomainException('Cannot change the role of the project owner.');
        }

        $project->members()->updateExistingPivot($user->id, ['role' => $role]);
    }
}
