<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        return view('admin.branches.index', [
            'branches' => Branch::query()->orderBy('sort_order')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function create()
    {
        return view('admin.branches.form', ['branch' => new Branch]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $branch = Branch::create($this->validated($request));
        $audit->log('branch.created', $branch, [], ['name' => $branch->name]);

        return redirect()->route('admin.branches.index')->with('success', 'शाखा जोड़ी गई।');
    }

    public function edit(Branch $branch)
    {
        return view('admin.branches.form', ['branch' => $branch]);
    }

    public function update(Request $request, Branch $branch, AuditLogger $audit)
    {
        $branch->update($this->validated($request));
        $audit->log('branch.updated', $branch, [], ['name' => $branch->name]);

        return redirect()->route('admin.branches.index')->with('success', 'शाखा अपडेट हो गई।');
    }

    public function destroy(Branch $branch, AuditLogger $audit)
    {
        $audit->log('branch.deleted', $branch, ['name' => $branch->name], []);
        $branch->delete();

        return redirect()->route('admin.branches.index')->with('success', 'शाखा हटा दी गई।');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'digits_between:10,15'],
            'email' => ['nullable', 'email', 'max:190'],
            'map_url' => ['nullable', 'url', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'शाखा का नाम आवश्यक है।',
            'map_url.url' => 'मान्य मैप लिंक भरें।',
        ]);

        $data['phone'] = $data['phone'] ?? null;
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
