<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentStatus;
use App\Http\Controllers\Controller;
use App\Models\MembershipPayment;
use App\Services\AuditLogger;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = MembershipPayment::query()
            ->with('member')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.trim((string) $request->input('q')).'%';
                $query->where(fn ($q) => $q->where('order_no', 'like', $term)
                    ->orWhere('receipt_no', 'like', $term)
                    ->orWhere('utr', 'like', $term)
                    ->orWhereHas('member', fn ($m) => $m->where('name', 'like', $term)->orWhere('membership_no', 'like', $term)));
            })
            ->latest()
            ->paginate(config('maku.pagination.admin'))
            ->withQueryString();

        return view('admin.payments.index', [
            'payments' => $payments,
            'statuses' => PaymentStatus::cases(),
            'filters' => $request->only(['q', 'status', 'type']),
            'totalPaid' => MembershipPayment::query()->where('status', 'paid')->sum('amount'),
            'pendingCount' => MembershipPayment::query()->where('status', 'pending')->count(),
        ]);
    }

    public function verify(Request $request, MembershipPayment $payment, PaymentService $payments, AuditLogger $audit)
    {
        if ($payment->status === PaymentStatus::Paid->value) {
            return back()->with('error', 'यह भुगतान पहले ही सत्यापित है।');
        }

        $payments->markPaid($payment, $payment->gateway_payment_id ?: 'MANUAL-'.$payment->id, [
            'verified_by' => $request->user()->id,
            'utr' => $payment->utr,
        ]);

        $audit->log('payment.verified', $payment, ['status' => 'pending'], ['status' => 'paid']);

        return back()->with('success', 'भुगतान सत्यापित कर सदस्यता सक्रिय कर दी गई।');
    }

    public function refund(Request $request, MembershipPayment $payment, AuditLogger $audit)
    {
        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $payment->update([
            'status' => PaymentStatus::Refunded->value,
            'payload' => array_merge($payment->payload ?? [], ['refund_note' => $data['note'] ?? null, 'refunded_at' => now()->toIso8601String()]),
        ]);

        $audit->log('payment.refunded', $payment, ['status' => 'paid'], ['status' => 'refunded']);

        return back()->with('success', 'भुगतान वापसी दर्ज कर दी गई।');
    }

    public function export(Request $request)
    {
        $query = MembershipPayment::query()
            ->with('member')
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->latest();

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['ऑर्डर संख्या', 'रसीद संख्या', 'सदस्य', 'सदस्यता संख्या', 'प्रकार', 'राशि', 'गेटवे', 'UTR', 'स्थिति', 'भुगतान तिथि']);

            $query->chunk(500, function ($payments) use ($out) {
                foreach ($payments as $payment) {
                    fputcsv($out, [
                        $payment->order_no,
                        $payment->receipt_no,
                        $payment->member?->name,
                        $payment->member?->membership_no,
                        $payment->type === 'renewal' ? 'नवीनीकरण' : 'नया',
                        $payment->amount,
                        $payment->gateway,
                        $payment->utr,
                        $payment->statusLabel(),
                        $payment->paid_at?->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($out);
        }, 'payments-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
