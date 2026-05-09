<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'task_id' => ['required', 'integer', 'exists:tasks,id'],
            'assigned_roommate_id' => ['required', 'integer', 'exists:roommates,id'],
            'status' => ['required', 'string', Rule::in(['done', 'not_done', 'done_by_other'])],
            'actual_roommate_id' => ['nullable', 'integer', 'exists:roommates,id', 'required_if:status,done_by_other'],
        ];
    }
}
