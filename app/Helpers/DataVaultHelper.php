<?php

use App\Services\DataVaultService;
use Illuminate\Support\Collection;

if (!function_exists('dataVaultItems')) {
    /**
     * Get Data Vault items for a category
     *
     * @param string $categoryCode
     * @return Collection
     */
    function dataVaultItems(string $categoryCode): Collection
    {
        return DataVaultService::getItems($categoryCode);
    }
}

if (!function_exists('dataVaultArray')) {
    /**
     * Get Data Vault items as array for select dropdown
     *
     * @param string $categoryCode
     * @return array
     */
    function dataVaultArray(string $categoryCode): array
    {
        return DataVaultService::getItemsAsArray($categoryCode);
    }
}

if (!function_exists('dataVaultLabel')) {
    /**
     * Get label for a Data Vault item
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return string|null
     */
    function dataVaultLabel(string $categoryCode, string $itemCode): ?string
    {
        return DataVaultService::getItemLabel($categoryCode, $itemCode);
    }
}

if (!function_exists('dataVaultColor')) {
    /**
     * Get color for a Data Vault item
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return string|null
     */
    function dataVaultColor(string $categoryCode, string $itemCode): ?string
    {
        return DataVaultService::getItemColor($categoryCode, $itemCode);
    }
}

if (!function_exists('dataVaultIcon')) {
    /**
     * Get icon for a Data Vault item
     *
     * @param string $categoryCode
     * @param string $itemCode
     * @return string|null
     */
    function dataVaultIcon(string $categoryCode, string $itemCode): ?string
    {
        return DataVaultService::getItemIcon($categoryCode, $itemCode);
    }
}
