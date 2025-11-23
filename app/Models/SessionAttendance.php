<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionAttendance extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'enrollment_id',
        'status',
        'attended_hours',
        'marked_by',
        'marked_at',
        'notes',
    ];

    protected $casts = [
        'attended_hours' => 'decimal:2',
        'marked_at' => 'datetime',
    ];

    /**
     * Get the session this attendance is for
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(CourseSession::class, 'session_id');
    }

    /**
     * Get the student (user) this attendance is for
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the enrollment this attendance is associated with
     */
    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(CourseEnrollment::class, 'enrollment_id');
    }

    /**
     * Get the teacher who marked the attendance
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    /**
     * Scope: Get present attendances
     */
    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    /**
     * Scope: Get absent attendances
     */
    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }

    /**
     * Scope: Get excused attendances
     */
    public function scopeExcused($query)
    {
        return $query->where('status', 'excused');
    }

    /**
     * Scope: Get late attendances
     */
    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    /**
     * Scope: Filter by session
     */
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope: Filter by student
     */
    public function scopeForStudent($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Mark as present
     */
    public function markAsPresent($markedBy, $hours = null)
    {
        $this->status = 'present';
        $this->attended_hours = $hours ?? $this->session->duration_hours ?? 0;
        $this->marked_by = $markedBy;
        $this->marked_at = now();
        $this->save();

        // Trigger enrollment recalculation
        $this->enrollment->calculateProgressFromAttendance();
    }

    /**
     * Mark as absent
     */
    public function markAsAbsent($markedBy, $notes = null)
    {
        $this->status = 'absent';
        $this->attended_hours = 0;
        $this->marked_by = $markedBy;
        $this->marked_at = now();
        $this->notes = $notes;
        $this->save();

        // Trigger enrollment recalculation
        $this->enrollment->calculateProgressFromAttendance();
    }

    /**
     * Mark as excused
     */
    public function markAsExcused($markedBy, $notes = null)
    {
        $this->status = 'excused';
        $this->attended_hours = 0;
        $this->marked_by = $markedBy;
        $this->marked_at = now();
        $this->notes = $notes;
        $this->save();

        // Trigger enrollment recalculation
        $this->enrollment->calculateProgressFromAttendance();
    }

    /**
     * Mark as late
     */
    public function markAsLate($markedBy, $hours = null)
    {
        $this->status = 'late';
        $this->attended_hours = $hours ?? ($this->session->duration_hours * 0.8) ?? 0; // 80% if late
        $this->marked_by = $markedBy;
        $this->marked_at = now();
        $this->save();

        // Trigger enrollment recalculation
        $this->enrollment->calculateProgressFromAttendance();
    }

    /**
     * Check if attendance is marked
     */
    public function isMarked(): bool
    {
        return !is_null($this->marked_at);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'present' => 'success',
            'absent' => 'danger',
            'excused' => 'warning',
            'late' => 'orange',
            default => 'secondary',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match($this->status) {
            'present' => 'fa-check',
            'absent' => 'fa-times',
            'excused' => 'fa-minus-circle',
            'late' => 'fa-clock',
            default => 'fa-question',
        };
    }
}
