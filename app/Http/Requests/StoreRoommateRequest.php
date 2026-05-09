<?php

namespace App\Http\Requests;

use App\Models\Roommate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoommateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /** @return array<string, array<int, mixed>> */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:30'],
            'avatar_slug' => ['nullable', 'string', Rule::in(Roommate::AVATARS)],
            'avatar_photo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
