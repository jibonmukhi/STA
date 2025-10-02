<?php

namespace App\Services;

use App\Models\DataVaultCategory;
use App\Models\DataVaultItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class DataVaultService
{
    /**
     * Get all items for a category as Collection
     *
     * @param string $categoryCode
     * @param string|null $locale
     * @return Collection
     */
    public static function getItems(string $categoryCode, ?string $locale = null): Collection
    {
        $locale = $locale ?? app()->getLocale();

        return Cache::remember("data_vault_{$categoryCode}_{$locale}", 3600, function () use ($categoryCode, $locale) {
            $category = DataVaultCategory::where('code', $categoryCode)
                ->where('is_active', true)
                ->first();

            if (!$category) {
                return collect([]);
            }

            return $category->activeItems()
                ->ordered()
                ->get()
                ->map(function ($item) use ($locale) {
                    return [
                        'id' => $item->id,
                        'code' => $item->code,
                        'label' => $locale === 'it' ? $item->label_it : $item->label_en,
                        'color' => $item->color,
                        'icon' => $item->icon,
                        'is_default' => $item->is_default,
                        'metadata' => $item->metadata,
                    ];
                });
        });
    }

    /**
     * Get items as array for select dropdown
     *
     * @param string $categoryCode
     * @param string|null $locale
     * @return array
     */
    public static function getItemsAsArray(string $categoryCode, ?string $locale = null): array
    {
        $items = self::getItems($categoryCode, $locale);

        return $items->pluck('label', 'code')->toArray();
    }

    /**
     * Get label for specific item
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @param string|null $locale
     * @return string|null
     */
    public static function getItemLabel(string $categoryCode, string $itemCode, ?string $locale = null): ?string
    {
        $items = self::getItems($categoryCode, $locale);

        $item = $items->firstWhere('code', $itemCode);

        return $item ? $item['label'] : null;
    }

    /**
     * Get color for specific item
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return string|null
     */
    public static function getItemColor(string $categoryCode, string $itemCode): ?string
    {
        $items = self::getItems($categoryCode);

        $item = $items->firstWhere('code', $itemCode);

        return $item ? $item['color'] : null;
    }

    /**
     * Get icon for specific item
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return string|null
     */
    public static function getItemIcon(string $categoryCode, string $itemCode): ?string
    {
        $items = self::getItems($categoryCode);

        $item = $items->firstWhere('code', $itemCode);

        return $item ? $item['icon'] : null;
    }

    /**
     * Get default item for category
     *
     * @param string $categoryCode
     * @return array|null
     */
    public static function getDefaultItem(string $categoryCode): ?array
    {
        $items = self::getItems($categoryCode);

        return $items->firstWhere('is_default', true);
    }

    /**
     * Clear cache for specific category
     *
     * @param string $categoryCode
     * @return void
     */
    public static function clearCache(string $categoryCode): void
    {
        Cache::forget("data_vault_{$categoryCode}_en");
        Cache::forget("data_vault_{$categoryCode}_it");
    }

    /**
     * Clear all data vault cache
     *
     * @return void
     */
    public static function clearAllCache(): void
    {
        $categories = DataVaultCategory::pluck('code');

        foreach ($categories as $categoryCode) {
            self::clearCache($categoryCode);
        }
    }

    /**
     * Check if item exists in category
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return bool
     */
    public static function itemExists(string $categoryCode, string $itemCode): bool
    {
        $items = self::getItems($categoryCode);

        return $items->contains('code', $itemCode);
    }

    /**
     * Get item by code
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return array|null
     */
    public static function getItem(string $categoryCode, string $itemCode): ?array
    {
        $items = self::getItems($categoryCode);

        return $items->firstWhere('code', $itemCode);
    }
}
