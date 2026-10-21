<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MenuItem;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

class MenuController extends Controller
{
    public function index()
    {
        return view('admin.menus.index', [
            'items' => MenuItem::query()->with('parent')->orderBy('sort_order')->get(),
        ]);
    }

    public function create()
    {
        return view('admin.menus.form', [
            'item' => new MenuItem,
            'parents' => MenuItem::query()->whereNull('parent_id')->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $this->validated($request);
        $item = MenuItem::create($data);
        Cache::forget('maku.menu');
        $audit->log('menu.created', $item, [], ['label' => $item->label]);

        return redirect()->route('admin.menus.index')->with('success', 'मेन्यू आइटम जोड़ा गया।');
    }

    public function edit(MenuItem $menu)
    {
        return view('admin.menus.form', [
            'item' => $menu,
            'parents' => MenuItem::query()->whereNull('parent_id')->where('id', '!=', $menu->id)->orderBy('sort_order')->get(),
        ]);
    }

    public function update(Request $request, MenuItem $menu, AuditLogger $audit)
    {
        $data = $this->validated($request, $menu);
        $old = $menu->only(['label', 'url', 'route_name', 'sort_order', 'is_active']);
        $menu->update($data);
        Cache::forget('maku.menu');
        $audit->log('menu.updated', $menu, $old, $data);

        return redirect()->route('admin.menus.index')->with('success', 'मेन्यू आइटम अपडेट हो गया।');
    }

    public function destroy(MenuItem $menu, AuditLogger $audit)
    {
        $audit->log('menu.deleted', $menu, ['label' => $menu->label], []);
        $menu->delete();
        Cache::forget('maku.menu');

        return redirect()->route('admin.menus.index')->with('success', 'मेन्यू आइटम हटा दिया गया।');
    }

    private function validated(Request $request, ?MenuItem $menu = null): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'url' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:100'],
            'parent_id' => ['nullable', 'integer', 'exists:menu_items,id', Rule::notIn(array_filter([$menu?->id]))],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
            'opens_new_tab' => ['nullable', 'boolean'],
        ], [
            'label.required' => 'लेबल आवश्यक है।',
            'parent_id.not_in' => 'कोई आइटम स्वयं का अभिभावक नहीं हो सकता।',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);
        $data['opens_new_tab'] = $request->boolean('opens_new_tab');

        return $data;
    }
}
