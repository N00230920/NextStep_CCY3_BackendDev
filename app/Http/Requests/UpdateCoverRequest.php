<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'application_id' => ['nullable', Rule::exists('applications', 'id')->where(fn ($q) => $q->where('user_id', $this->user()->id)),], // Ensure the application belongs to the authenticated user
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }
}