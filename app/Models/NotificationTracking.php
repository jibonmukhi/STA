<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationTracking extends Model
{
    protected $table = 'notification_tracking';

    protected $fillable = [
        'notification_id',
        'recipient_user_id',
        'company_id',
        'company_note_id',
        'notification_type',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the recipient user
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_user_id');
    }

    /**
     * Get the company
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the company note
     */
    public function companyNote()
    {
        return $this->belongsTo(CompanyNote::class);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }

    /**
     * Mark notification as delivered
     */
    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    /**
     * Mark notification as failed
     */
    public function markAsFailed($errorMessage = null)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Scope for read notifications
     */
    public function scopeRead($query)
    {
        return $query->where('status', 'read');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->whereIn('status', ['sent', 'delivered']);
    }

    /**
     * Scope for failed notifications
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if notification is read
     */
    public function isRead(): bool
    {
        return $this->status === 'read';
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return in_array($this->status, ['sent', 'delivered']);
    }
}
