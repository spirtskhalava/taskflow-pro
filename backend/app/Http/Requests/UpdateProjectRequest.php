<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'deadline'    => 'nullable|date',
            'color'       => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
        ];
    }
}
