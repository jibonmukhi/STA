<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Company;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'mobile',
        'gender',
        'date_of_birth',
        'tax_id_code',
        'status',
        'address',
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
            'status' => 'boolean',
        ];
    }

    public function companies()
    {
        return $this->belongsToMany(Company::class, 'user_companies')
                    ->withPivot(['is_primary', 'role_in_company', 'joined_at'])
                    ->withTimestamps();
    }

    public function primaryCompany()
    {
        return $this->belongsToMany(Company::class, 'user_companies')
                    ->withPivot(['is_primary', 'role_in_company', 'joined_at'])
                    ->wherePivot('is_primary', true)
                    ->withTimestamps();
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->surname);
    }

    public function getAgeAttribute()
    {
        return $this->date_of_birth ? $this->date_of_birth->age : null;
    }
}
