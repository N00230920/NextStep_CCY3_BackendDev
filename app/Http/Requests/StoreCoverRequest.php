<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCoverRequest extends FormRequest
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