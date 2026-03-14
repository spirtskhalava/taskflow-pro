<?php

namespace App\Listeners;

use App\Events\TaskStatusChanged;
use App\Jobs\SendTaskNotificationJob;
use App\Models\User;

class NotifyTeamMembers
{
    public function handle(TaskStatusChanged $event): void
    {
        if ($event->newStatus !== 'done') {
            return;
        }

        $task = $event->task->load('project.members', 'reporter', 'assignee');

        $recipients = $task->project->members
            ->filter(fn (User $user) => $user->id !== auth()->id())
            ->merge($task->reporter ? collect([$task->reporter]) : collect())
            ->unique('id');

        foreach ($recipients as $recipient) {
            SendTaskNotificationJob::dispatch($task, $recipient, 'task_completed')
                ->onQueue('notifications');
        }
    }
}
