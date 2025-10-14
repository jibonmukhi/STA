<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class CompanyInvitation extends Model
{
    protected $fillable = [
        'token',
        'company_name',
        'company_email',
        'company_phone',
        'company_piva',
        'company_ateco_code',
        'manager_username',
        'manager_name',
        'manager_surname',
        'manager_email',
        'temporary_password',
        'status',
        'expires_at',
        'accepted_at',
        'invited_by',
        'company_id',
        'user_id',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    /**
     * Generate a unique secure token for the invitation
     */
    public static function generateToken(): string
    {
        return Str::random(64);
    }

    /**
     * Generate a random temporary password
     */
    public static function generateTemporaryPassword(): string
    {
        return Str::random(12);
    }

    /**
     * Check if the invitation is expired
     */
    public function isExpired(): bool
    {
        return $this->expires_at->isPast() || $this->status === 'expired';
    }

    /**
     * Check if the invitation is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending' && !$this->isExpired();
    }

    /**
     * Check if the invitation has been accepted
     */
    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    /**
     * Mark the invitation as accepted
     */
    public function markAsAccepted(int $companyId, int $userId): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
            'company_id' => $companyId,
            'user_id' => $userId,
        ]);
    }

    /**
     * Mark the invitation as expired
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Get the user who sent the invitation
     */
    public function inviter()
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    /**
     * Get the company created from this invitation
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the user created from this invitation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get pending invitations
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
                     ->where('expires_at', '>', now());
    }

    /**
     * Scope to get expired invitations
     */
    public function scopeExpired($query)
    {
        return $query->where(function($q) {
            $q->where('status', 'expired')
              ->orWhere('expires_at', '<=', now());
        });
    }

    /**
     * Scope to get accepted invitations
     */
    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    /**
     * Get formatted status badge for display
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending' => $this->isExpired()
                ? '<span class="badge bg-danger">Expired</span>'
                : '<span class="badge bg-warning">Pending</span>',
            'accepted' => '<span class="badge bg-success">Accepted</span>',
            'expired' => '<span class="badge bg-danger">Expired</span>',
            'rejected' => '<span class="badge bg-secondary">Rejected</span>',
            default => '<span class="badge bg-secondary">Unknown</span>'
        };
    }
}
