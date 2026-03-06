<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    public function update(User $user, Task $task): bool
    {
        $project = $task->project;

        return $project->isOwnedBy($user)
            || $project->hasMember($user);
    }

    public function delete(User $user, Task $task): bool
    {
        return $task->reporter_id === $user->id
            || $task->project->isOwnedBy($user);
    }
}
