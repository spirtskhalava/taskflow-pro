<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'           => 'required|string|max:500',
            'description'     => 'nullable|string',
            'status'          => 'nullable|in:todo,in_progress,in_review,done',
            'priority'        => 'nullable|in:low,medium,high,urgent',
            'assignee_id'     => 'nullable|exists:users,id',
            'due_date'        => 'nullable|date',
            'estimated_hours' => 'nullable|numeric|min:0.5|max:999',
        ];
    }
}
