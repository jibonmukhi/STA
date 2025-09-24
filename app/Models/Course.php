<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'course_code',
        'description',
        'objectives',
        'category',
        'level',
        'duration_hours',
        'credits',
        'price',
        'instructor',
        'prerequisites',
        'delivery_method',
        'max_participants',
        'is_active',
        'is_mandatory',
        'available_from',
        'available_until',
    ];

    protected $casts = [
        'credits' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_mandatory' => 'boolean',
        'available_from' => 'date',
        'available_until' => 'date',
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

    public static function getCategories(): array
    {
        return [
            'programming' => 'Programming',
            'web_development' => 'Web Development',
            'mobile_development' => 'Mobile Development',
            'data_science' => 'Data Science',
            'cybersecurity' => 'Cybersecurity',
            'project_management' => 'Project Management',
            'design' => 'Design',
            'business' => 'Business',
            'marketing' => 'Marketing',
            'other' => 'Other'
        ];
    }

    public static function getLevels(): array
    {
        return [
            'beginner' => 'Beginner',
            'intermediate' => 'Intermediate',
            'advanced' => 'Advanced'
        ];
    }

    public static function getDeliveryMethods(): array
    {
        return [
            'online' => 'Online',
            'offline' => 'Offline',
            'hybrid' => 'Hybrid'
        ];
    }

    public function courseEvents(): HasMany
    {
        return $this->hasMany(CourseEvent::class);
    }
}
