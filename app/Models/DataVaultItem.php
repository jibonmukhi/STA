<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataVaultItem extends Model
{
    protected $fillable = [
        'category_id',
        'code',
        'label_en',
        'label_it',
        'color',
        'icon',
        'sort_order',
        'is_default',
        'is_system',
        'is_active',
        'metadata',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function category(): BelongsTo
    {
        return $this->belongsTo(DataVaultCategory::class, 'category_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('label_en');
    }

    public function scopeForCategory($query, $categoryCode)
    {
        return $query->whereHas('category', function($q) use ($categoryCode) {
            $q->where('code', $categoryCode);
        });
    }

    // Accessors
    public function getLabelAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'it' ? $this->label_it : $this->label_en;
    }

    public function canDelete(): bool
    {
        return !$this->is_system;
    }

    public function getBadgeColorAttribute(): string
    {
        return $this->color ?? 'secondary';
    }
}
