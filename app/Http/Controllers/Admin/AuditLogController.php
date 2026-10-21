<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.audit-logs.index', [
            'logs' => $this->query($request)
                ->with('user')
                ->latest('created_at')
                ->paginate(50)
                ->withQueryString(),
            'users' => User::query()->orderBy('name')->get(),
            'actions' => AuditLog::query()->select('action')->distinct()->orderBy('action')->pluck('action'),
            'filters' => $request->only(['action', 'user', 'from', 'to']),
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->query($request)->with('user')->latest('created_at');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['समय', 'उपयोगकर्ता', 'गतिविधि', 'लक्ष्य', 'पहले', 'बाद', 'IP']);

            $query->chunk(500, function ($logs) use ($out) {
                foreach ($logs as $log) {
                    fputcsv($out, [
                        $log->created_at?->format('Y-m-d H:i:s'),
                        $log->user?->name ?? 'सिस्टम',
                        $log->actionLabel().' ('.$log->action.')',
                        $log->targetLabel(),
                        $log->old_values ? json_encode($log->old_values, JSON_UNESCAPED_UNICODE) : '',
                        $log->new_values ? json_encode($log->new_values, JSON_UNESCAPED_UNICODE) : '',
                        $log->ip,
                    ]);
                }
            });

            fclose($out);
        }, 'audit-log-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function query(Request $request)
    {
        return AuditLog::query()
            ->when($request->filled('action'), fn ($q) => $q->where('action', $request->input('action')))
            ->when($request->filled('user'), fn ($q) => $q->where('user_id', $request->input('user')))
            ->when($request->filled('from'), fn ($q) => $q->whereDate('created_at', '>=', $request->input('from')))
            ->when($request->filled('to'), fn ($q) => $q->whereDate('created_at', '<=', $request->input('to')));
    }
}
