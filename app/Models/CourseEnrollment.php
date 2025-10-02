<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'final_score',
        'grade',
        'notes',
    ];

    protected $casts = [
        'enrolled_at' => 'date',
        'completed_at' => 'date',
        'progress_percentage' => 'decimal:2',
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
}
