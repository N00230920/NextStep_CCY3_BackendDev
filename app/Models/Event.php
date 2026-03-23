<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'event';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */

    protected $fillable = [
        'user_id',
        'application_id',
        'title',
        'event_type',
        'description',
        'event_date',
        'is_all_day',
        'event_time',
        'location',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_all_day' => 'boolean',
    ];

        /**
        * Get the user that owns the event.
        */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
