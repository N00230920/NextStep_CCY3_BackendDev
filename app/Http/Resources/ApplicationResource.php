<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApplicationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'cv_id' => $this->cv_id,
            'company_name' => $this->company_name,
            'position' => $this->position,
            'location' => $this->location,
            'contact_email' => $this->contact_email,
            'salary' => $this->salary,
            'status' => $this->status,
            'job_type' => $this->job_type,
            'job_url' => $this->job_url,
            'notes' => $this->notes,
            'applied_date' => $this->applied_date ? $this->applied_date->format('d/m/y') : null,
            'created_at' => $this->created_at->format('d/m/y'),
            'updated_at' => $this->updated_at->format('d/m/y'),
        ];
    }
}
