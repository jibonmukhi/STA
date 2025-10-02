<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;
use App\Traits\HasAuditLog;

class Certificate extends Model
{
    use HasFactory, SoftDeletes, HasAuditLog;

    protected $fillable = [
        'user_id',
        'company_id',
        'name',
        'description',
        'subject',
        'certificate_number',
        'issue_date',
        'expiration_date',
        'duration_months',
        'training_organization',
        'training_organization_code',
        'instructor_name',
        'training_organization_address',
        'certificate_type',
        'level',
        'hours_completed',
        'credits',
        'score',
        'grade',
        'regulatory_body',
        'compliance_standard',
        'renewal_required',
        'renewal_period_months',
        'next_renewal_date',
        'status',
        'verification_code',
        'issuer_signature',
        'verified_at',
        'certificate_file_path',
        'transcript_file_path',
        'supporting_documents',
        'notes',
        'metadata',
        'is_public',
        'language',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
        'next_renewal_date' => 'date',
        'verified_at' => 'datetime',
        'supporting_documents' => 'array',
        'metadata' => 'array',
        'renewal_required' => 'boolean',
        'is_public' => 'boolean',
        'hours_completed' => 'decimal:2',
        'credits' => 'decimal:2',
        'score' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        // Generate verification code and certificate number on creation
        static::creating(function ($certificate) {
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = 'CERT-' . strtoupper(uniqid());
            }
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = 'CN-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
            }
        });

        // Update next renewal date when expiration date changes
        static::saving(function ($certificate) {
            if ($certificate->renewal_required && $certificate->renewal_period_months && $certificate->expiration_date) {
                $certificate->next_renewal_date = $certificate->expiration_date->subMonths($certificate->renewal_period_months);
            }
        });
    }

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')->orWhere('expiration_date', '<', now());
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('expiration_date', '<=', now()->addDays($days))
                    ->where('expiration_date', '>', now())
                    ->where('status', 'active');
    }

    public function scopeByType($query, $type)
    {
        return $query->where('certificate_type', $type);
    }

    public function scopeByTrainingOrganization($query, $organization)
    {
        return $query->where('training_organization', 'like', '%' . $organization . '%');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Accessors
    public function getIsExpiredAttribute(): bool
    {
        return $this->expiration_date < now() || $this->status === 'expired';
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expiration_date <= now()->addDays(30) && $this->expiration_date > now() && $this->status === 'active';
    }

    public function getDaysUntilExpirationAttribute(): int
    {
        return $this->expiration_date ? now()->diffInDays($this->expiration_date, false) : 0;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        $color = dataVaultColor('certificate_status', $this->status);
        return $color ? 'bg-' . $color : 'bg-light';
    }

    public function getFormattedStatusAttribute(): string
    {
        return dataVaultLabel('certificate_status', $this->status) ?? ucfirst($this->status);
    }

    public function getFormattedTypeAttribute(): string
    {
        return dataVaultLabel('certificate_type', $this->certificate_type) ?? ucfirst($this->certificate_type);
    }

    public function getFormattedLevelAttribute(): string
    {
        return dataVaultLabel('certificate_level', $this->level) ?? ($this->level ? ucfirst($this->level) : '-');
    }

    // Mutators
    public function setVerificationCodeAttribute($value)
    {
        $this->attributes['verification_code'] = $value ?: 'CERT-' . strtoupper(uniqid());
    }

    public function setCertificateNumberAttribute($value)
    {
        $this->attributes['certificate_number'] = $value ?: 'CN-' . date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    // Helper Methods
    public function markAsExpired(): bool
    {
        return $this->update(['status' => 'expired']);
    }

    public function markAsActive(): bool
    {
        return $this->update(['status' => 'active']);
    }

    public function revoke(?string $reason = null): bool
    {
        $metadata = $this->metadata ?? [];
        $metadata['revocation_reason'] = $reason;
        $metadata['revoked_at'] = now()->toISOString();

        return $this->update([
            'status' => 'revoked',
            'metadata' => $metadata
        ]);
    }

    public function renew(Carbon $newExpirationDate, array $additionalData = []): bool
    {
        return $this->update(array_merge([
            'expiration_date' => $newExpirationDate,
            'status' => 'active',
            'next_renewal_date' => $this->renewal_required && $this->renewal_period_months
                ? $newExpirationDate->subMonths($this->renewal_period_months)
                : null,
        ], $additionalData));
    }

    public function hasFile(): bool
    {
        return !empty($this->certificate_file_path) && file_exists(storage_path('app/' . $this->certificate_file_path));
    }

    public function getFileUrl(): ?string
    {
        return $this->hasFile() ? asset('storage/' . $this->certificate_file_path) : null;
    }

    public static function getCertificateTypes(): array
    {
        return dataVaultArray('certificate_type');
    }

    public static function getCertificateLevels(): array
    {
        return dataVaultArray('certificate_level');
    }

    public static function getCertificateStatuses(): array
    {
        return dataVaultArray('certificate_status');
    }
}