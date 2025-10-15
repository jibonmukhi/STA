<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyNote extends Model
{
    protected $fillable = [
        'company_id',
        'sent_by',
        'subject',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
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
