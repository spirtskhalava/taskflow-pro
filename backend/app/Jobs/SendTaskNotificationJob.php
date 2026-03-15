<?php

namespace App\Jobs;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Notification;

class SendTaskNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 60;

    public function __construct(
        private readonly Task $task,
        private readonly User $recipient,
        private readonly string $type
    ) {
    }

    public function handle(): void
    {
        // Create in-app notification
        $this->recipient->notifications()->create([
            'type'    => $this->type,
            'data'    => [
                'task_id'    => $this->task->id,
                'task_title' => $this->task->title,
                'project_id' => $this->task->project_id,
                'message'    => $this->buildMessage(),
            ],
            'read_at' => null,
        ]);
    }

    private function buildMessage(): string
    {
        return match ($this->type) {
            'task_completed' => "Task \"{$this->task->title}\" has been marked as done.",
            'task_assigned'  => "You have been assigned to \"{$this->task->title}\".",
            default          => "Task \"{$this->task->title}\" was updated.",
        };
    }
}
