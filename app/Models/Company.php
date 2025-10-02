<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Traits\HasAuditLog;

class Company extends Model
{
    use HasAuditLog;
    protected $fillable = [
        'ateco_code',
        'name',
        'email',
        'phone',
        'piva',
        'website',
        'address',
        'logo',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($company) {
            if (empty($company->name)) {
                throw new \InvalidArgumentException('Company name is required.');
            }
        });

        static::updating(function ($company) {
            if (empty($company->name)) {
                throw new \InvalidArgumentException('Company name is required.');
            }
        });
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            $logoPath = storage_path('app/public/' . $this->logo);
            if (file_exists($logoPath)) {
                return asset('storage/' . $this->logo);
            }
        }
        
        return asset('images/default-company-logo.png');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('active', false);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_companies')
                    ->withPivot(['is_primary', 'role_in_company', 'joined_at'])
                    ->withTimestamps();
    }
}
