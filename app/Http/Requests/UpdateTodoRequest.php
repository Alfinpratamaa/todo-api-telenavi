<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'assignee' => 'nullable|string|max:255',
            'due_date' => 'sometimes|required|date|after_or_equal:today',
            'time_tracked' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'sometimes|required|in:low,medium,high',
        ];
    }

    public function messages(): array
    {
        return [
            'due_date.after_or_equal' => 'Due date must be today or a future date.',
            'priority.required' => 'Priority is required.',
            'priority.in' => 'Priority must be low, medium, or high.',
        ];
    }
}
