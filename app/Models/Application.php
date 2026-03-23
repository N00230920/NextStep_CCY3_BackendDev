<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    protected $fillable = [
        'user_id',
        'cv_id',
        'company_name',
        'position',
        'location',
        'contact_email',
        'salary',
        'status',
        'job_type',
        'job_url',
        'notes',
        'applied_date',
        ];

    protected $casts = [
        'applied_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cv(): BelongsTo
    {
        return $this->belongsTo(Cv::class);
    }

    public function covers(): HasMany
    {
        return $this->hasMany(Cover::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}
