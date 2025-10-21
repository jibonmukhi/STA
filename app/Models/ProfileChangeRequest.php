<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Services\AuditLogService;

class ProfileChangeRequest extends Model
{
    protected $fillable = [
        'user_id',
        'requested_changes',
        'current_data',
        'status',
        'request_message',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];

    protected $casts = [
        'requested_changes' => 'array',
        'current_data' => 'array',
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the user who made the request
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the manager who reviewed the request
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get fields that changed
     */
    public function getChangedFields(): array
    {
        return array_keys($this->requested_changes);
    }

    /**
     * Get comparison data for a field
     */
    public function getFieldComparison(string $field): array
    {
        return [
            'current' => $this->current_data[$field] ?? null,
            'requested' => $this->requested_changes[$field] ?? null,
        ];
    }

    /**
     * Check if a field has changed
     */
    public function hasFieldChanged(string $field): bool
    {
        $current = $this->current_data[$field] ?? null;
        $requested = $this->requested_changes[$field] ?? null;
        return $current !== $requested;
    }

    /**
     * Approve the profile change request
     */
    public function approve(User $reviewer): bool
    {
        $this->status = 'approved';
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        $this->save();

        // Apply changes to user
        $user = $this->user;

        // Handle photo upload if it exists in requested changes
        if (isset($this->requested_changes['photo']) && $this->requested_changes['photo']) {
            $user->photo = $this->requested_changes['photo'];
        }

        // Update other fields
        $fieldsToUpdate = array_diff_key(
            $this->requested_changes,
            ['photo' => true] // Exclude photo as it's handled separately
        );

        foreach ($fieldsToUpdate as $field => $value) {
            $user->$field = $value;
        }

        // If email changed, reset verification
        if (isset($this->requested_changes['email']) &&
            $this->requested_changes['email'] !== $this->current_data['email']) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Log the approval
        AuditLogService::logCustom(
            'profile_change_approved',
            "Profile change request approved by {$reviewer->full_name}",
            'users',
            'info',
            [
                'user_id' => $user->id,
                'request_id' => $this->id,
                'changed_fields' => $this->getChangedFields(),
                'changes' => $this->requested_changes,
                'approved_by' => $reviewer->id,
            ]
        );

        return true;
    }

    /**
     * Reject the profile change request
     */
    public function reject(User $reviewer, string $reason = null): bool
    {
        $this->status = 'rejected';
        $this->reviewed_by = $reviewer->id;
        $this->reviewed_at = now();
        $this->rejection_reason = $reason;
        $this->save();

        // Log the rejection
        AuditLogService::logCustom(
            'profile_change_rejected',
            "Profile change request rejected by {$reviewer->full_name}",
            'users',
            'info',
            [
                'user_id' => $this->user_id,
                'request_id' => $this->id,
                'rejected_by' => $reviewer->id,
                'rejection_reason' => $reason,
            ]
        );

        return true;
    }
}
