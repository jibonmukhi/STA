<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CourseSession extends Model
{
    protected $fillable = [
        'course_id',
        'session_title',
        'session_date',
        'start_time',
        'end_time',
        'duration_hours',
        'location',
        'description',
        'status',
        'max_participants',
        'session_order',
    ];

    protected $casts = [
        'session_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'duration_hours' => 'decimal:2',
    ];

    /**
     * Get the course that owns the session
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get all attendances for this session
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(SessionAttendance::class, 'session_id');
    }

    /**
     * Get students who attended this session (present or late)
     */
    public function presentStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_attendances', 'session_id', 'user_id')
            ->whereIn('session_attendances.status', ['present', 'late']);
    }

    /**
     * Get students who were absent
     */
    public function absentStudents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'session_attendances', 'session_id', 'user_id')
            ->where('session_attendances.status', 'absent');
    }

    /**
     * Calculate duration in hours from start and end time
     */
    public function calculateDuration(): float
    {
        if ($this->start_time && $this->end_time) {
            $start = \Carbon\Carbon::parse($this->start_time);
            $end = \Carbon\Carbon::parse($this->end_time);
            return $start->diffInMinutes($end) / 60;
        }
        return 0;
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    /**
     * Mark attendance for multiple students
     */
    public function markAttendance(array $attendanceData, $markedBy): bool
    {
        foreach ($attendanceData as $data) {
            $attendance = SessionAttendance::updateOrCreate(
                [
                    'session_id' => $this->id,
                    'user_id' => $data['user_id'],
                ],
                [
                    'enrollment_id' => $data['enrollment_id'],
                    'status' => $data['status'],
                    'attended_hours' => $data['attended_hours'] ?? $this->duration_hours,
                    'marked_by' => $markedBy,
                    'marked_at' => now(),
                    'notes' => $data['notes'] ?? null,
                ]
            );

            // Trigger recalculation
            $attendance->enrollment->calculateProgressFromAttendance();
        }

        return true;
    }

    /**
     * Get attendance statistics
     */
    public function getAttendanceStats(): array
    {
        // Only count active enrollments (matching what's displayed in the attendance view)
        $totalEnrolled = $this->course->enrollments()
            ->whereIn('status', ['enrolled', 'in_progress', 'completed'])
            ->count();
        $present = $this->attendances()->where('status', 'present')->count();
        $absent = $this->attendances()->where('status', 'absent')->count();
        $late = $this->attendances()->where('status', 'late')->count();
        $excused = $this->attendances()->where('status', 'excused')->count();
        $notMarked = $totalEnrolled - ($present + $absent + $late + $excused);

        return [
            'total_enrolled' => $totalEnrolled,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'excused' => $excused,
            'not_marked' => $notMarked,
            'marked' => $present + $absent + $late + $excused,
        ];
    }

    /**
     * Check if all attendance is marked
     */
    public function canBeClosed(): bool
    {
        $stats = $this->getAttendanceStats();
        return $stats['not_marked'] === 0 && $stats['total_enrolled'] > 0;
    }

    /**
     * Close the session
     */
    public function closeSession(): bool
    {
        if (!$this->canBeClosed()) {
            return false;
        }

        return $this->update(['status' => 'completed']);
    }

    /**
     * Check if session is completed
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get attendance completion percentage
     */
    public function getAttendanceCompletionPercentage(): float
    {
        $stats = $this->getAttendanceStats();
        if ($stats['total_enrolled'] === 0) {
            return 0;
        }

        return ($stats['marked'] / $stats['total_enrolled']) * 100;
    }
}
