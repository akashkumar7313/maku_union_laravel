<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Services\AuditLogger;
use Illuminate\Http\Request;

class TeamMemberController extends Controller
{
    use HandlesUploads;

    public function index()
    {
        return view('admin.team.index', [
            'members' => TeamMember::query()->orderBy('sort_order')->paginate(config('maku.pagination.admin')),
        ]);
    }

    public function create()
    {
        return view('admin.team.form', ['member' => new TeamMember]);
    }

    public function store(Request $request, AuditLogger $audit)
    {
        $data = $this->validated($request);
        $data['photo'] = $this->storeUpload($request->file('photo'), 'team');

        $member = TeamMember::create($data);
        $audit->log('team_member.created', $member, [], ['name' => $member->name]);

        return redirect()->route('admin.team-members.index')->with('success', 'पदाधिकारी जोड़े गए।');
    }

    public function edit(TeamMember $teamMember)
    {
        return view('admin.team.form', ['member' => $teamMember]);
    }

    public function update(Request $request, TeamMember $teamMember, AuditLogger $audit)
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $this->deleteUpload($teamMember->photo);
            $data['photo'] = $this->storeUpload($request->file('photo'), 'team');
        }

        $teamMember->update($data);
        $audit->log('team_member.updated', $teamMember, [], ['name' => $teamMember->name]);

        return redirect()->route('admin.team-members.index')->with('success', 'पदाधिकारी विवरण अपडेट हो गया।');
    }

    public function destroy(TeamMember $teamMember, AuditLogger $audit)
    {
        $this->deleteUpload($teamMember->photo);
        $audit->log('team_member.deleted', $teamMember, ['name' => $teamMember->name], []);
        $teamMember->delete();

        return redirect()->route('admin.team-members.index')->with('success', 'पदाधिकारी हटा दिए गए।');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'position' => ['nullable', 'string', 'max:150'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
            'is_active' => ['nullable', 'boolean'],
        ], [
            'name.required' => 'नाम आवश्यक है।',
        ]);

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active', true);

        return $data;
    }
}
