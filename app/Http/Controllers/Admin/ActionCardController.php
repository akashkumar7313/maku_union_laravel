<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActionCard;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ActionCardController extends Controller
{
    public function index()
    {
        return view('admin.action-cards.index', [
            'cards' => ActionCard::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $card = ActionCard::create($this->validated($request));
        $audit->log('action_card.created', $card, [], ['title' => $card->title]);

        return back()->with('success', 'एक्शन कार्ड जोड़ा गया।');
    }

    public function update(Request $request, ActionCard $actionCard, AuditLogger $audit)
    {
        $actionCard->update($this->validated($request));
        $audit->log('action_card.updated', $actionCard, [], ['title' => $actionCard->title]);

        return back()->with('success', 'एक्शन कार्ड अपडेट हो गया।');
    }

    public function destroy(ActionCard $actionCard, AuditLogger $audit)
    {
        $audit->log('action_card.deleted', $actionCard, ['title' => $actionCard->title], []);
        $actionCard->delete();

        return back()->with('success', 'एक्शन कार्ड हटा दिया गया।');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:300'],
            'color' => ['required', Rule::in(['green', 'blue', 'orange', 'navy'])],
            'icon' => ['nullable', 'string', 'max:60'],
            'url' => ['required', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'title.required' => 'शीर्षक आवश्यक है।',
            'url.required' => 'लिंक आवश्यक है।',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
