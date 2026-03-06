<?php

namespace App\Policies;

use App\Enums\ProjectRole;
use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user) || $project->hasMember($user);
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Project $project): bool
    {
        if ($project->isOwnedBy($user)) {
            return true;
        }

        $role = $project->members()
            ->where('users.id', $user->id)
            ->first()
            ?->pivot
            ?->role;

        return $role && in_array($role, [ProjectRole::Admin->value, ProjectRole::Owner->value]);
    }

    public function delete(User $user, Project $project): bool
    {
        return $project->isOwnedBy($user);
    }
}
