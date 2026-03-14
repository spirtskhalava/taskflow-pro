<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskStatusChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Task $task,
        public readonly string $oldStatus,
        public readonly string $newStatus
    ) {
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("project.{$this->task->project_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'task_id'    => $this->task->id,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'updated_by' => auth()->id(),
        ];
    }
}
