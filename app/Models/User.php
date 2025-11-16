<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Company;
use App\Traits\HasAuditLog;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasAuditLog;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'date_of_birth',
        'place_of_birth',
        'country',
        'email',
        'username', // Unique username for login
        'phone',
        'mobile', // Keep both phone and mobile for backward compatibility
        'gender',
        'cf', // Codice Fiscale
        'photo',
        'address',
        'status',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'date_of_birth' => 'date',
        ];
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_companies')
                    ->withPivot(['is_primary', 'role_in_company', 'joined_at', 'percentage'])
                    ->withTimestamps();
    }

    public function primaryCompany()
    {
        return $this->belongsToMany(Company::class, 'user_companies')
                    ->withPivot(['is_primary', 'role_in_company', 'joined_at', 'percentage'])
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }

    public function getPrimaryCompanyAttribute()
    {
        return $this->primaryCompany()->first();
    }

    public function getPrimaryCompanyLogoAttribute()
    {
        $primaryCompany = $this->primary_company;
        if ($primaryCompany) {
            return $primaryCompany->logo_url;
        }
        
        return asset('images/default-logo.png');
    }

    public function getTotalPercentageAttribute()
    {
        return $this->companies->sum('pivot.percentage');
    }

    public function hasValidPercentageAllocation()
    {
        return $this->total_percentage == 100;
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->surname);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            $photoPath = storage_path('app/public/' . $this->photo);
            if (file_exists($photoPath)) {
                return asset('storage/' . $this->photo);
            }
        }
        
        return asset('images/default-avatar.png');
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'active' => '<span class="badge bg-success">Active</span>',
            'inactive' => '<span class="badge bg-secondary">Inactive</span>',
            'parked' => '<span class="badge bg-warning">Parked (Pending Approval)</span>',
            default => '<span class="badge bg-secondary">Unknown</span>'
        };
    }

    public function isParked()
    {
        return $this->status === 'parked';
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function needsApproval()
    {
        return $this->isParked();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeParked($query)
    {
        return $query->where('status', 'parked');
    }

    // Teacher relationships
    public function teacherCourses()
    {
        return $this->hasMany(Course::class, 'teacher_id');
    }

    // Student relationships
    public function enrolledCourses()
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
                    ->withPivot(['status', 'enrolled_at', 'completed_at', 'progress_percentage', 'final_score', 'grade', 'notes'])
                    ->withTimestamps();
    }

    public function courseEnrollments()
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    public function getFormattedRoleAttribute()
    {
        if ($this->roles->isEmpty()) {
            return 'No Role';
        }

        $roleNames = $this->roles->map(function($role) {
            return match($role->name) {
                'sta_manager' => 'STA Manager',
                'company_manager' => 'Company Manager',
                'end_user' => 'End User',
                'teacher' => 'Teacher',
                default => ucwords(str_replace('_', ' ', $role->name))
            };
        });

        return $roleNames->implode(', ');
    }

    public static function formatRoleName($roleName)
    {
        return match($roleName) {
            'sta_manager' => 'STA Manager',
            'company_manager' => 'Company Manager',
            'end_user' => 'End User',
            'teacher' => 'Teacher',
            default => ucwords(str_replace('_', ' ', $roleName))
        };
    }
}
