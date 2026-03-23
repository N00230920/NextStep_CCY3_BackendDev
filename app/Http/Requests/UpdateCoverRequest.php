<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCoverRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'application_id' => 'nullable|exists:applications,id',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }
}