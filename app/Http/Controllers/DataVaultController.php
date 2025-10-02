<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataVaultCategory;
use App\Services\DataVaultService;
use App\Services\AuditLogService;

class DataVaultController extends Controller
{
    public function index()
    {
        $categories = DataVaultCategory::withCount('items')
            ->ordered()
            ->get();

        return view('data-vault.index', compact('categories'));
    }

    public function create()
    {
        return view('data-vault.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:data_vault_categories,code',
            'name_en' => 'required|string|max:255',
            'name_it' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $category = DataVaultCategory::create($validated);

        DataVaultService::clearCache($category->code);

        // Log category creation
        AuditLogService::logCustom(
            'data_vault_category_created',
            "Created Data Vault category: {$category->name_en}",
            'data_vault',
            'info',
            [
                'category_id' => $category->id,
                'category_code' => $category->code,
                'category_name' => $category->name_en,
                'created_by' => auth()->id()
            ]
        );

        return redirect()->route('data-vault.index')
            ->with('success', __('data_vault.category_created_successfully'));
    }

    public function edit(DataVaultCategory $category)
    {
        return view('data-vault.edit', compact('category'));
    }

    public function update(Request $request, DataVaultCategory $category)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:data_vault_categories,code,' . $category->id,
            'name_en' => 'required|string|max:255',
            'name_it' => 'required|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        // Store old values for audit log
        $oldValues = $category->only(['code', 'name_en', 'name_it', 'is_active']);

        $category->update($validated);

        DataVaultService::clearCache($category->code);

        // Log category update
        AuditLogService::logCustom(
            'data_vault_category_updated',
            "Updated Data Vault category: {$category->name_en}",
            'data_vault',
            'info',
            [
                'category_id' => $category->id,
                'old_values' => $oldValues,
                'new_values' => $validated,
                'updated_by' => auth()->id()
            ]
        );

        return redirect()->route('data-vault.index')
            ->with('success', __('data_vault.category_updated_successfully'));
    }

    public function destroy(DataVaultCategory $category)
    {
        if ($category->is_system) {
            return redirect()->route('data-vault.index')
                ->with('error', __('data_vault.cannot_delete_system_category'));
        }

        $categoryName = $category->name_en;
        $categoryCode = $category->code;
        $itemCount = $category->items()->count();

        DataVaultService::clearCache($category->code);

        // Log category deletion
        AuditLogService::logCustom(
            'data_vault_category_deleted',
            "Deleted Data Vault category: {$categoryName}",
            'data_vault',
            'warning',
            [
                'category_id' => $category->id,
                'category_code' => $categoryCode,
                'category_name' => $categoryName,
                'had_items' => $itemCount,
                'deleted_by' => auth()->id()
            ]
        );

        $category->delete();

        return redirect()->route('data-vault.index')
            ->with('success', __('data_vault.category_deleted_successfully'));
    }
}
