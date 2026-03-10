<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class AttachmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'url'        => Storage::url($this->path),
            'mime_type'  => $this->mime_type,
            'size'       => $this->size,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}
