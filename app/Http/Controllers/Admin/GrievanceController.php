<?php

namespace App\Http\Controllers\Admin;

use App\Enums\GrievanceStatus;
use App\Http\Controllers\Controller;
use App\Models\Grievance;
use App\Models\GrievanceDocument;
use App\Models\User;
use App\Services\AuditLogger;
use App\Services\GrievanceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class GrievanceController extends Controller
{
    public function index(Request $request)
    {
        $grievances = Grievance::query()
            ->with(['category', 'assignee'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->input('priority')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->input('q')).'%';
                $query->where(fn ($q) => $q->where('case_no', 'like', $term)
                    ->orWhere('name', 'like', $term)
                    ->orWhere('phone', 'like', $term)
                    ->orWhere('subject', 'like', $term));
            })
            ->latest()
            ->paginate(config('maku.pagination.admin'))
            ->withQueryString();

        return view('admin.grievances.index', [
            'grievances' => $grievances,
            'statuses' => GrievanceStatus::cases(),
            'filters' => $request->only(['q', 'status', 'priority']),
        ]);
    }

    public function show(Grievance $grievance)
    {
        return view('admin.grievances.show', [
            'grievance' => $grievance->load(['category', 'assignee', 'member', 'updates.user', 'documents']),
            'statuses' => GrievanceStatus::cases(),
            'officers' => User::query()->where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Grievance $grievance, GrievanceService $service, AuditLogger $audit)
    {
        $data = $request->validate([
            'status' => ['required', Rule::enum(GrievanceStatus::class)],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'assigned_to' => ['nullable', Rule::exists('users', 'id')],
            'note' => ['nullable', 'string', 'max:2000'],
            'is_public' => ['nullable', 'boolean'],
        ], [
            'status.required' => 'स्थिति चुनें।',
            'priority.required' => 'प्राथमिकता चुनें।',
        ]);

        $old = $grievance->only(['status', 'priority', 'assigned_to']);

        $grievance->update([
            'priority' => $data['priority'],
            'assigned_to' => $data['assigned_to'] ?: null,
        ]);

        if ($data['status'] !== $grievance->status || ! empty($data['note'])) {
            $service->addUpdate(
                $grievance,
                $data['status'],
                $data['note'] ?? null,
                $request->user(),
                (bool) ($data['is_public'] ?? false)
            );
        }

        $audit->log('grievance.updated', $grievance, $old, $grievance->fresh()->only(['status', 'priority', 'assigned_to']));

        return back()->with('success', 'शिकायत विवरण अपडेट हो गया।');
    }

    public function uploadDocument(Request $request, Grievance $grievance, GrievanceService $service, AuditLogger $audit)
    {
        $request->validate([
            'document' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:4096'],
        ], [
            'document.required' => 'दस्तावेज़ चुनें।',
            'document.mimes' => 'दस्तावेज़ JPG, PNG, PDF, DOC या DOCX में होना चाहिए।',
        ]);

        $document = $service->storeDocument($grievance, $request->file('document'), $request->user());
        $audit->log('grievance.document_uploaded', $document, [], ['name' => $document->original_name]);

        return back()->with('success', 'दस्तावेज़ अपलोड हो गया।');
    }

    public function downloadDocument(Grievance $grievance, GrievanceDocument $document)
    {
        abort_unless($document->grievance_id === $grievance->id, 404);

        return Storage::disk(config('maku.uploads.disk'))->download($document->path, $document->original_name);
    }

    public function deleteDocument(Grievance $grievance, GrievanceDocument $document, AuditLogger $audit)
    {
        abort_unless($document->grievance_id === $grievance->id, 404);

        Storage::disk(config('maku.uploads.disk'))->delete($document->path);
        $audit->log('grievance.document_deleted', $document, ['name' => $document->original_name], []);
        $document->delete();

        return back()->with('success', 'दस्तावेज़ हटा दिया गया।');
    }

    public function export(Request $request)
    {
        $query = Grievance::query()
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->input('priority')))
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['शिकायत संख्या', 'नाम', 'मोबाइल', 'विषय', 'प्राथमिकता', 'स्थिति', 'दर्ज तिथि', 'समाधान तिथि']);

            $query->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $grievance) {
                    fputcsv($out, [
                        $grievance->case_no,
                        $grievance->name,
                        $grievance->phone,
                        $grievance->subject,
                        $grievance->priority,
                        $grievance->statusLabel(),
                        $grievance->created_at?->format('Y-m-d'),
                        $grievance->resolved_at?->format('Y-m-d'),
                    ]);
                }
            });

            fclose($out);
        }, 'grievances-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
