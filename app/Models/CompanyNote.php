<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyNote extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
        'user_data',
        'sent_by',
        'subject',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'user_data' => 'array',
    ];

    /**
     * Get the company that owns this note
     */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the STA manager who sent this note
     */
    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    /**
     * Get the user this note is about
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get notification tracking records for this note
     */
    public function notificationTracking()
    {
        return $this->hasMany(NotificationTracking::class, 'company_note_id');
    }

    /**
     * Mark the note as read
     */
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope for unread notes
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for read notes
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }
}
