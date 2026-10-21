<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MemberStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreMemberRequest;
use App\Models\Member;
use App\Models\WorkerCategory;
use App\Services\AuditLogger;
use App\Services\MembershipService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $members = Member::query()
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->input('q')).'%';
                $query->where(fn ($q) => $q
                    ->where('name', 'like', $term)
                    ->orWhere('membership_no', 'like', $term)
                    ->orWhere('phone', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('category'), fn ($q) => $q->where('worker_category_id', $request->input('category')))
            ->latest()
            ->paginate(config('maku.pagination.admin'))
            ->withQueryString();

        return view('admin.members.index', [
            'members' => $members,
            'categories' => WorkerCategory::query()->ordered()->get(),
            'statuses' => MemberStatus::cases(),
            'filters' => $request->only(['q', 'status', 'category']),
        ]);
    }

    public function create()
    {
        return view('admin.members.create', [
            'categories' => WorkerCategory::query()->active()->ordered()->get(),
        ]);
    }

    public function store(
        StoreMemberRequest $request,
        MembershipService $memberships,
        PaymentService $payments,
        AuditLogger $audit
    ) {
        $data = $request->validated();
        $markActive = (bool) ($data['mark_active'] ?? false);
        unset($data['mark_active']);

        $member = $memberships->register($data, $request->user());

        if ($markActive) {
            $payment = $payments->createForMember($member, 'new');
            $payment = $payments->useOfflineAmount($payment);
            $payments->markPaid($payment, 'OFFICE-'.$payment->id, ['gateway' => 'office']);
            $member->refresh();
        }

        $audit->log('member.created', $member, [], ['membership_no' => $member->membership_no]);

        return redirect()->route('admin.members.show', $member)
            ->with('success', 'सदस्य सफलतापूर्वक जोड़ा गया। सदस्यता संख्या: '.$member->membership_no);
    }

    public function show(Member $member)
    {
        $member->load(['category', 'payments', 'renewals', 'grievances', 'creator']);

        return view('admin.members.show', [
            'member' => $member,
            'cardUrl' => URL::temporarySignedRoute('membership.card', now()->addMinutes(30), ['member' => $member->id]),
        ]);
    }

    public function edit(Member $member)
    {
        return view('admin.members.edit', [
            'member' => $member,
            'categories' => WorkerCategory::query()->ordered()->get(),
            'statuses' => MemberStatus::cases(),
        ]);
    }

    public function update(Request $request, Member $member, AuditLogger $audit)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:150'],
            'guardian_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['required', 'digits_between:10,15'],
            'email' => ['nullable', 'email', 'max:190'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'dob' => ['nullable', 'date', 'before:today'],
            'address' => ['required', 'string', 'min:5', 'max:500'],
            'district' => ['required', 'string', 'max:100'],
            'block' => ['nullable', 'string', 'max:100'],
            'worker_category_id' => ['required', Rule::exists('worker_categories', 'id')],
            'aadhaar_number' => ['nullable', 'digits:12'],
            'valid_from' => ['nullable', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:valid_from'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'id_document' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:4096'],
        ], [
            'name.required' => 'नाम आवश्यक है।',
            'phone.required' => 'मोबाइल नंबर आवश्यक है।',
            'address.required' => 'पता आवश्यक है।',
            'worker_category_id.required' => 'श्रमिक श्रेणी चुनें।',
            'valid_until.after_or_equal' => 'अवधि समाप्ति तिथि प्रारंभ तिथि के बाद होनी चाहिए।',
        ]);

        $data['phone'] = clean_digits($data['phone']);

        if (blank($data['aadhaar_number'] ?? null)) {
            unset($data['aadhaar_number']);
        } else {
            $data['aadhaar_number'] = clean_digits($data['aadhaar_number']);
        }

        if ($request->hasFile('photo')) {
            if ($member->photo) {
                Storage::disk(config('maku.uploads.disk'))->delete($member->photo);
            }
            $data['photo'] = $request->file('photo')->store('members/photos', config('maku.uploads.disk'));
        }

        if ($request->hasFile('id_document')) {
            if ($member->id_document) {
                Storage::disk(config('maku.uploads.disk'))->delete($member->id_document);
            }
            $data['id_document'] = $request->file('id_document')->store('members/documents', config('maku.uploads.disk'));
        }

        $old = $member->only(array_keys($data));
        $member->update($data);

        $audit->log('member.updated', $member, $old, $data);

        return redirect()->route('admin.members.show', $member)->with('success', 'सदस्य विवरण अपडेट हो गया।');
    }

    public function updateStatus(Request $request, Member $member, MembershipService $memberships, AuditLogger $audit)
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(MemberStatus::class)],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($data['status'] === MemberStatus::Active->value) {
            $member->valid_from ??= today();
            $member->valid_until ??= today()->addYears((int) config('maku.membership.duration_years', 1));
            $member->save();
        }

        $old = $member->status;
        $member->update(['status' => $data['status']]);

        if (! empty($data['note'])) {
            $member->update([
                'notes' => trim(($member->notes ? $member->notes."\n" : '').$data['note']),
            ]);
        }

        $audit->log('member.status_updated', $member, ['status' => $old], ['status' => $data['status']]);

        return back()->with('success', 'सदस्य की स्थिति अपडेट हो गई।');
    }

    public function recordPayment(Request $request, Member $member, PaymentService $payments, AuditLogger $audit)
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(['new', 'renewal'])],
            'mark_paid' => ['nullable', 'boolean'],
        ]);

        $payment = $payments->createForMember($member, $data['type']);

        if ($request->boolean('mark_paid')) {
            $payment = $payments->useOfflineAmount($payment);
            $payments->markPaid($payment, 'OFFICE-'.$payment->id, ['gateway' => 'office']);
        }

        $audit->log('member.payment_recorded', $member, [], ['order_no' => $payment->order_no, 'amount' => $payment->amount]);

        return back()->with('success', 'भुगतान प्रविष्टि बन गई: '.$payment->order_no);
    }

    public function destroy(Member $member, AuditLogger $audit)
    {
        $member->update(['status' => MemberStatus::Rejected->value]);
        $audit->log('member.archived', $member, [], ['status' => MemberStatus::Rejected->value]);

        return redirect()->route('admin.members.index')
            ->with('success', 'सदस्य रिकॉर्ड सुरक्षित रखते हुए निष्क्रिय कर दिया गया है।');
    }

    public function export(Request $request)
    {
        $query = Member::query()
            ->with('category')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->input('q')).'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)
                    ->orWhere('membership_no', 'like', $term)
                    ->orWhere('phone', 'like', $term));
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('category'), fn ($q) => $q->where('worker_category_id', $request->input('category')))
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['सदस्यता संख्या', 'नाम', 'पिता/पति', 'मोबाइल', 'श्रेणी', 'जनपद', 'सदस्यता तिथि', 'वैधता', 'स्थिति']);

            $query->chunk(500, function ($members) use ($out) {
                foreach ($members as $member) {
                    fputcsv($out, [
                        $member->membership_no,
                        $member->name,
                        $member->guardian_name,
                        $member->phone,
                        $member->category?->name,
                        $member->district,
                        $member->joined_on?->format('Y-m-d'),
                        $member->valid_until?->format('Y-m-d'),
                        $member->statusLabel(),
                    ]);
                }
            });

            fclose($out);
        }, 'members-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
