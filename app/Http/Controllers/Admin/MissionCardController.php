<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MissionCard;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class MissionCardController extends Controller
{
    public function index()
    {
        return view('admin.mission-cards.index', [
            'cards' => MissionCard::query()->orderBy('sort_order')->get(),
        ]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $card = MissionCard::create($this->validated($request));
        $audit->log('mission_card.created', $card, [], ['title' => $card->title]);

        return back()->with('success', 'मिशन कार्ड जोड़ा गया।');
    }

    public function update(Request $request, MissionCard $missionCard, AuditLogger $audit)
    {
        $missionCard->update($this->validated($request));
        $audit->log('mission_card.updated', $missionCard, [], ['title' => $missionCard->title]);

        return back()->with('success', 'मिशन कार्ड अपडेट हो गया।');
    }

    public function destroy(MissionCard $missionCard, AuditLogger $audit)
    {
        $audit->log('mission_card.deleted', $missionCard, ['title' => $missionCard->title], []);
        $missionCard->delete();

        return back()->with('success', 'मिशन कार्ड हटा दिया गया।');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:60'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
