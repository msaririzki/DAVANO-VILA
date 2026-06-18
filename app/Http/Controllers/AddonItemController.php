<?php

namespace App\Http\Controllers;

use App\Models\AddonItem;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddonItemController extends Controller
{
    public function index(): View
    {
        return view('addon-items.index', [
            'addonItems' => AddonItem::query()->orderBy('category')->orderBy('name')->get(),
            'categoryOptions' => AddonItem::categoryOptions(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:'.implode(',', array_keys(AddonItem::categoryOptions()))],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $addonItem = AddonItem::query()->create([
            'name' => $validated['name'],
            'type' => AddonItem::typeForCategory($validated['category']),
            'category' => $validated['category'],
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::record(
            $request,
            'addon_item.created',
            'Menambahkan add-on '.$addonItem->name,
            $addonItem,
            null,
            $addonItem->only(['name', 'type', 'category', 'price', 'is_active']),
        );

        return back()->with('status', 'Menu add-on berhasil ditambahkan.');
    }

    public function update(Request $request, AddonItem $addonItem): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:'.implode(',', array_keys(AddonItem::categoryOptions()))],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $oldValues = $addonItem->only(['name', 'type', 'category', 'price', 'is_active']);

        $addonItem->update([
            'name' => $validated['name'],
            'type' => AddonItem::typeForCategory($validated['category']),
            'category' => $validated['category'],
            'price' => $validated['price'],
            'is_active' => $request->boolean('is_active'),
        ]);

        AuditLogger::record(
            $request,
            'addon_item.updated',
            'Mengubah add-on '.$addonItem->name,
            $addonItem,
            $oldValues,
            $addonItem->only(['name', 'type', 'category', 'price', 'is_active']),
        );

        return back()->with('status', 'Menu add-on berhasil diperbarui.');
    }
}
