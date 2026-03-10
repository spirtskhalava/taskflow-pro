<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'project_id'      => $this->project_id,
            'title'           => $this->title,
            'description'     => $this->description,
            'status'          => $this->status,
            'priority'        => $this->priority,
            'position'        => $this->position,
            'due_date'        => $this->due_date?->toDateString(),
            'completed_at'    => $this->completed_at?->toISOString(),
            'estimated_hours' => $this->estimated_hours,
            'comments_count'  => $this->when(isset($this->comments_count), $this->comments_count),
            'assignee'        => new UserResource($this->whenLoaded('assignee')),
            'reporter'        => new UserResource($this->whenLoaded('reporter')),
            'project'         => new ProjectResource($this->whenLoaded('project')),
            'comments'        => CommentResource::collection($this->whenLoaded('comments')),
            'attachments'     => AttachmentResource::collection($this->whenLoaded('attachments')),
            'created_at'      => $this->created_at->toISOString(),
            'updated_at'      => $this->updated_at->toISOString(),
        ];
    }
}
