<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name'                  => $this->name,
            'description'           => $this->description,
            'status'                => $this->status,
            'color'                 => $this->color,
            'deadline'              => $this->deadline?->toDateString(),
            'is_archived'           => $this->is_archived,
            'completion_percentage' => $this->completion_percentage,
            'tasks_count'           => $this->when(isset($this->tasks_count), $this->tasks_count),
            'completed_tasks_count' => $this->when(isset($this->completed_tasks_count), $this->completed_tasks_count),
            'owner'                 => new UserResource($this->whenLoaded('owner')),
            'members'               => UserResource::collection($this->whenLoaded('members')),
            'tasks'                 => TaskResource::collection($this->whenLoaded('tasks')),
            'created_at'            => $this->created_at->toISOString(),
            'updated_at'            => $this->updated_at->toISOString(),
        ];
    }
}
