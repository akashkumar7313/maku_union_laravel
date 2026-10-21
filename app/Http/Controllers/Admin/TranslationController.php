<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Translation;
use App\Services\AuditLogger;
use App\Services\TranslationService;
use Illuminate\Http\Request;

class TranslationController extends Controller
{
    public function index(Request $request, TranslationService $service)
    {
        $translations = Translation::query()
            ->when($request->filled('q'), fn ($q) => $q->where('key', 'like', '%'.trim((string) $request->input('q')).'%')
                ->orWhere('value', 'like', '%'.trim((string) $request->input('q')).'%'))
            ->when($request->filled('reviewed'), fn ($q) => $q->where('reviewed', $request->input('reviewed') === '1'))
            ->latest()
            ->paginate(config('maku.pagination.admin'))
            ->withQueryString();

        return view('admin.translations.index', [
            'translations' => $translations,
            'status' => $service->status(),
            'filters' => $request->only(['q', 'reviewed']),
        ]);
    }

    public function update(Request $request, Translation $translation, AuditLogger $audit)
    {
        $data = $request->validate([
            'value' => ['required', 'string', 'max:5000'],
            'reviewed' => ['nullable', 'boolean'],
        ], [
            'value.required' => 'अनुवाद आवश्यक है।',
        ]);

        $data['reviewed'] = $request->boolean('reviewed');
        $translation->update($data);
        $audit->log('translation.updated', $translation, [], ['key' => $translation->key, 'reviewed' => $translation->reviewed]);

        return back()->with('success', 'अनुवाद सुरक्षित हो गया।');
    }

    public function review(Translation $translation, AuditLogger $audit)
    {
        $translation->update(['reviewed' => true]);
        $audit->log('translation.reviewed', $translation, [], ['key' => $translation->key]);

        return back()->with('success', 'अनुवाद समीक्षित के रूप में चिह्नित हो गया।');
    }
}
