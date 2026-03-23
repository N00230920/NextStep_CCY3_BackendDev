<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cv_id' => 'nullable|exists:cvs,id',
            'company_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'contact_email' => 'nullable|email|max:255',
            'salary' => 'nullable|integer',
            'status' => 'required|in:applied,interview,offer,rejected,ghosted',
            'job_type' => 'nullable|in:full-time,part-time,internship,contract',
            'job_url' => 'nullable|string',
            'notes' => 'nullable|string',
            'applied_date' => 'nullable|date',
        ];
    }
}