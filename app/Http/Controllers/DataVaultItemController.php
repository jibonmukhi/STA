<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataVaultCategory;
use App\Models\DataVaultItem;
use App\Services\DataVaultService;

class DataVaultItemController extends Controller
{
    public function index(DataVaultCategory $category)
    {
        $items = $category->items()->ordered()->get();

        return view('data-vault.items.index', compact('category', 'items'));
    }

    public function store(Request $request, DataVaultCategory $category)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'unique:data_vault_items,code,NULL,id,category_id,' . $category->id
            ],
            'label_en' => 'required|string|max:255',
            'label_it' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['category_id'] = $category->id;

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            DataVaultItem::where('category_id', $category->id)
                ->update(['is_default' => false]);
        }

        DataVaultItem::create($validated);

        DataVaultService::clearCache($category->code);

        return redirect()->route('data-vault.items.index', $category)
            ->with('success', __('data_vault.item_created_successfully'));
    }

    public function update(Request $request, DataVaultCategory $category, DataVaultItem $item)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
                'unique:data_vault_items,code,' . $item->id . ',id,category_id,' . $category->id
            ],
            'label_en' => 'required|string|max:255',
            'label_it' => 'required|string|max:255',
            'color' => 'nullable|string|max:50',
            'icon' => 'nullable|string|max:100',
            'sort_order' => 'nullable|integer',
            'is_default' => 'boolean',
            'is_active' => 'boolean',
        ]);

        // If this is set as default, unset other defaults
        if ($request->boolean('is_default')) {
            DataVaultItem::where('category_id', $category->id)
                ->where('id', '!=', $item->id)
                ->update(['is_default' => false]);
        }

        $item->update($validated);

        DataVaultService::clearCache($category->code);

        return redirect()->route('data-vault.items.index', $category)
            ->with('success', __('data_vault.item_updated_successfully'));
    }

    public function destroy(DataVaultCategory $category, DataVaultItem $item)
    {
        if ($item->is_system) {
            return redirect()->route('data-vault.items.index', $category)
                ->with('error', __('data_vault.cannot_delete_system_item'));
        }

        $item->delete();

        DataVaultService::clearCache($category->code);

        return redirect()->route('data-vault.items.index', $category)
            ->with('success', __('data_vault.item_deleted_successfully'));
    }

    public function reorder(Request $request, DataVaultCategory $category)
    {
        $order = $request->input('order', []);

        foreach ($order as $index => $itemId) {
            DataVaultItem::where('id', $itemId)
                ->where('category_id', $category->id)
                ->update(['sort_order' => $index + 1]);
        }

        DataVaultService::clearCache($category->code);

        return response()->json(['success' => true]);
    }
}
