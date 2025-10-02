<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataVaultCategory extends Model
{
    protected $fillable = [
        'code',
        'name_en',
        'name_it',
        'description',
        'is_system',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    // Relationships
    public function items(): HasMany
    {
        return $this->hasMany(DataVaultItem::class, 'category_id');
    }

    public function activeItems(): HasMany
    {
        return $this->hasMany(DataVaultItem::class, 'category_id')->where('is_active', true)->orderBy('sort_order');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name_en');
    }

    // Accessors
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return $locale === 'it' ? $this->name_it : $this->name_en;
    }

    public function canDelete(): bool
    {
        return !$this->is_system;
    }
}
