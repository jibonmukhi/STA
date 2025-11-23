<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasAuditLog;

class CourseEnrollment extends Model
{
    use HasAuditLog;
    protected $fillable = [
        'course_id',
        'user_id',
        'company_id',
        'status',
        'enrolled_at',
        'completed_at',
        'progress_percentage',
        'attended_hours',
        'total_required_hours',
        'attendance_percentage',
        'final_score',
        'grade',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
        'progress_percentage' => 'decimal:2',
        'attended_hours' => 'decimal:2',
        'total_required_hours' => 'decimal:2',
        'attendance_percentage' => 'decimal:2',
        'final_score' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($enrollment) {
            if (empty($enrollment->enrolled_at)) {
                $enrollment->enrolled_at = now()->toDateString();
            }
        });
    }

    // Relationships
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

    public function attendances(): HasMany
    {
        return $this->hasMany(SessionAttendance::class, 'enrollment_id');
    }

    // Scopes
    public function scopeEnrolled($query)
    {
        return $query->where('status', 'enrolled');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeForCourse($query, int $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Helper Methods
    public function markAsCompleted(?float $finalScore = null, ?string $grade = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'progress_percentage' => 100,
            'final_score' => $finalScore,
            'grade' => $grade,
        ]);
    }

    public function updateProgress(float $percentage): bool
    {
        return $this->update([
            'progress_percentage' => min($percentage, 100),
            'status' => $percentage >= 100 ? 'completed' : 'in_progress',
        ]);
    }

    public function getStatusBadgeAttribute(): string
    {
        $label = dataVaultLabel('enrollment_status', $this->status) ?? 'Unknown';
        $color = dataVaultColor('enrollment_status', $this->status) ?? 'secondary';

        return '<span class="badge bg-' . $color . '">' . $label . '</span>';
    }

    /**
     * Calculate progress based on attendance
     */
    public function calculateProgressFromAttendance(): bool
    {
        $totalSessions = $this->course->sessions()->count();

        if ($totalSessions === 0) {
            return false;
        }

        // Count attended sessions (present or late)
        $attendedSessions = $this->attendances()
            ->whereIn('status', ['present', 'late'])
            ->count();

        // Calculate progress percentage
        $progressPercentage = ($attendedSessions / $totalSessions) * 100;

        // Calculate total hours
        $attendedHours = $this->attendances()->sum('attended_hours');
        $totalRequiredHours = $this->course->sessions()->sum('duration_hours');

        // Calculate attendance percentage
        $attendancePercentage = ($attendedSessions / $totalSessions) * 100;

        // Update enrollment
        $updated = $this->update([
            'progress_percentage' => min($progressPercentage, 100),
            'attended_hours' => $attendedHours,
            'total_required_hours' => $totalRequiredHours,
            'attendance_percentage' => $attendancePercentage,
            'status' => $progressPercentage >= 100 ? 'completed' : ($progressPercentage > 0 ? 'in_progress' : 'enrolled'),
        ]);

        // Auto-complete if 100%
        if ($progressPercentage >= 100 && !$this->completed_at) {
            $this->update(['completed_at' => now()]);
        }

        return $updated;
    }

    /**
     * Recalculate attended hours
     */
    public function recalculateAttendedHours(): float
    {
        $attendedHours = $this->attendances()->sum('attended_hours');
        $this->update(['attended_hours' => $attendedHours]);
        return $attendedHours;
    }

    /**
     * Get attendance rate
     */
    public function getAttendanceRate(): float
    {
        $totalSessions = $this->course->sessions()->count();

        if ($totalSessions === 0) {
            return 0;
        }

        $attendedSessions = $this->attendances()
            ->whereIn('status', ['present', 'late'])
            ->count();

        return ($attendedSessions / $totalSessions) * 100;
    }

    /**
     * Get number of sessions attended
     */
    public function getSessionsAttendedAttribute(): int
    {
        return $this->attendances()
            ->whereIn('status', ['present', 'late'])
            ->count();
    }

    /**
     * Get total number of sessions
     */
    public function getTotalSessionsAttribute(): int
    {
        return $this->course->sessions()->count();
    }
}
