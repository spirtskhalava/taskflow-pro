<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TaskCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly Task $task)
    {
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("project.{$this->task->project_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'task.created';
    }
}
