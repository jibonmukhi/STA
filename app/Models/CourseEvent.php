<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class CourseEvent extends Model
{
    protected $fillable = [
        'title',
        'description',
        'course_id',
        'user_id',
        'company_id',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
        'location',
        'status',
        'max_participants',
        'registered_participants',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'registered_participants' => 'integer',
        'max_participants' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByMonth(Builder $query, int $year, int $month): Builder
    {
        return $query->whereYear('start_date', $year)
                    ->whereMonth('start_date', $month);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('start_date', '>=', now()->toDateString())
                    ->where('status', 'scheduled');
    }
}
