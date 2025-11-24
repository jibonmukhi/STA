<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Traits\HasAuditLog;

class Course extends Model
{
    use HasFactory, HasAuditLog;

    protected $fillable = [
        'parent_course_id',
        'title',
        'course_code',
        'color',
        'description',
        'objectives',
        'category',
        'level',
        'duration_hours',
        'credits',
        'price',
        'instructor',
        'teacher_id',
        'prerequisites',
        'delivery_method',
        'max_participants',
        'is_active',
        'is_mandatory',
        'status',
        'available_from',
        'available_until',
        'start_date',
        'start_time',
        'end_date',
        'end_time',
    ];

    protected $casts = [
        'credits' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'available_from' => 'date',
        'available_until' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope to get only master courses (templates)
     */
    public function scopeMasters($query)
    {
        return $query->whereNull('parent_course_id');
    }

    /**
     * Scope to get only course instances (started courses)
     */
    public function scopeInstances($query)
    {
        return $query->whereNotNull('parent_course_id');
    }

    public static function getCategories(): array
    {
        return dataVaultArray('course_category');
    }

    public static function getLevels(): array
    {
        return dataVaultArray('course_level');
    }

    public static function getDeliveryMethods(): array
    {
        return dataVaultArray('delivery_method');
    }

    public static function getStatuses(): array
    {
        return dataVaultArray('course_status');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function courseEvents(): HasMany
    {
        return $this->hasMany(CourseEvent::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_teacher', 'course_id', 'teacher_id')
                    ->withPivot('is_primary')
                    ->withTimestamps();
    }

    public function primaryTeacher()
    {
        return $this->teachers()->wherePivot('is_primary', true)->first();
    }

    /**
     * Parent course (master/template) relationship
     */
    public function parentCourse(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'parent_course_id');
    }

    /**
     * Course instances (started courses) relationship
     */
    public function instances(): HasMany
    {
        return $this->hasMany(Course::class, 'parent_course_id');
    }

    /**
     * Check if this is a master course (template)
     */
    public function isMaster(): bool
    {
        return $this->parent_course_id === null;
    }

    /**
     * Check if this is a course instance (started course)
     */
    public function isInstance(): bool
    {
        return $this->parent_course_id !== null;
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
                    ->withPivot(['status', 'enrolled_at', 'completed_at', 'progress_percentage', 'final_score', 'grade', 'notes'])
                    ->withTimestamps();
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class)->orderBy('order');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(CourseSession::class)->orderBy('session_order')->orderBy('session_date')->orderBy('start_time');
    }

    public function companyAssignments(): HasMany
    {
        return $this->hasMany(CourseCompanyAssignment::class);
    }

    public function assignedCompanies()
    {
        return $this->belongsToMany(Company::class, 'course_company_assignments')
                    ->withPivot(['assigned_by', 'assigned_date', 'due_date', 'is_mandatory', 'notes'])
                    ->withTimestamps();
    }

    public function scopeByTeacher($query, int $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    public function getEnrolledStudentsCountAttribute(): int
    {
        return $this->enrollments()->whereIn('status', ['enrolled', 'in_progress'])->count();
    }

    public function getCompletedStudentsCountAttribute(): int
    {
        return $this->enrollments()->completed()->count();
    }

    /**
     * Get the color for this course. If not set, generate one based on course ID
     */
    public function getColorAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Generate consistent color based on course ID
        $colors = ['primary', 'success', 'danger', 'warning', 'info', 'purple', 'pink', 'indigo', 'teal', 'orange'];
        return $colors[$this->id % count($colors)];
    }
}
