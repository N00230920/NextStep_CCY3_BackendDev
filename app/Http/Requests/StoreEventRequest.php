<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
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
            'event_type' => 'required|in:interview,reminder,assessment,call,deadline',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'is_all_day' => 'required|boolean',
            'event_time' => 'nullable|date_format:H:i|required_if:is_all_day,false',
            'location' => 'nullable|string|max:255',
        ];
    }
}