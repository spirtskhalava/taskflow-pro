<?php

namespace App\Jobs;

use App\Models\Project;
use App\Models\User;
use App\Notifications\ProjectInvitationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendProjectInvitationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(
        private readonly Project $project,
        private readonly User $invitee,
        private readonly User $inviter
    ) {
    }

    public function handle(): void
    {
        $this->invitee->notify(
            new ProjectInvitationNotification($this->project, $this->inviter)
        );
    }
}
