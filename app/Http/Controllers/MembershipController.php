<?php

namespace App\Http\Controllers;

use App\Http\Requests\LookupMembershipRequest;
use App\Http\Requests\StoreMembershipRequest;
use App\Models\Member;
use App\Models\MembershipPayment;
use App\Models\WorkerCategory;
use App\Services\AuditLogger;
use App\Services\MembershipService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class MembershipController extends Controller
{
    public function create()
    {
        abort_if(setting('membership_open', '1') === '0', 403, 'सदस्यता पंजीकरण इस समय बंद है।');

        return view('membership.register', [
            'categories' => WorkerCategory::query()->active()->ordered()->get(),
            'fee' => (float) setting('membership_fee', config('maku.membership.fee')),
        ]);
    }

    public function store(
        StoreMembershipRequest $request,
        MembershipService $memberships,
        PaymentService $payments,
        AuditLogger $audit
    ) {
        abort_if(setting('membership_open', '1') === '0', 403, 'सदस्यता पंजीकरण इस समय बंद है।');

        $member = $memberships->register(
            collect($request->validated())->except(['terms', 'website'])->all()
        );

        $payment = $payments->createForMember($member, 'new');

        $audit->log('membership.registered', $member, [], ['membership_no' => $member->membership_no]);

        return redirect($this->signedPaymentUrl($payment))
            ->with('success', 'आपका पंजीकरण दर्ज हो गया है। सदस्यता संख्या: '.$member->membership_no.'. अब भुगतान पूरा करें।');
    }

    public function lookupForm()
    {
        return view('membership.lookup');
    }

    public function forgotForm()
    {
        return view('membership.forgot');
    }

    public function forgot(Request $request, MembershipService $memberships)
    {
        $data = $request->validate([
            'aadhaar_number' => ['required', 'digits:12'],
            'phone' => ['required', 'digits_between:10,15'],
        ], [
            'aadhaar_number.required' => 'कृपया पंजीकृत आधार नंबर भरें।',
            'aadhaar_number.digits' => 'आधार नंबर 12 अंकों का होना चाहिए।',
            'phone.required' => 'कृपया पंजीकृत मोबाइल नंबर भरें।',
            'phone.digits_between' => 'मोबाइल नंबर 10 से 15 अंकों का होना चाहिए।',
        ]);

        $member = $memberships->findByAadhaarAndPhone($data['aadhaar_number'], $data['phone']);

        if (! $member) {
            throw ValidationException::withMessages([
                'aadhaar_number' => 'इस आधार नंबर और मोबाइल नंबर से कोई पंजीकरण नहीं मिला। कृपया पंजीकरण के समय दिया गया आधार नंबर और मोबाइल नंबर भरें।',
            ]);
        }

        return redirect(URL::temporarySignedRoute('membership.card', now()->addHour(), ['member' => $member->id]))
            ->with('success', 'आपकी सदस्यता संख्या: <strong>'.e($member->membership_no).'</strong>. नीचे भुगतान की स्थिति देखें।');
    }

    public function lookup(LookupMembershipRequest $request, MembershipService $memberships)
    {
        $member = $memberships->findByCredentials(
            $request->validated('membership_no'),
            $request->validated('phone')
        );

        if (! $member) {
            throw ValidationException::withMessages([
                'membership_no' => 'इस सदस्यता संख्या और मोबाइल नंबर से कोई सदस्य नहीं मिला।',
            ]);
        }

        return redirect(URL::temporarySignedRoute('membership.card', now()->addHour(), ['member' => $member->id]));
    }

    public function card(Member $member)
    {
        $member->load(['category', 'payments' => fn ($q) => $q->latest(), 'renewals' => fn ($q) => $q->latest()]);

        return view('membership.card', [
            'member' => $member,
            'latestPayment' => $member->payments->first(),
            'renewUrl' => URL::temporarySignedRoute('membership.renew.form', now()->addHour(), ['member' => $member->id]),
        ]);
    }

    public function renewForm(Member $member)
    {
        return view('membership.renew', [
            'member' => $member,
            'fee' => (float) setting('renewal_fee', config('maku.membership.renewal_fee')),
            'action' => URL::temporarySignedRoute('membership.renew.store', now()->addHour(), ['member' => $member->id]),
        ]);
    }

    public function renew(Request $request, Member $member, PaymentService $payments, AuditLogger $audit)
    {
        if ($member->status === 'suspended') {
            return back()->with('error', 'निलंबित सदस्यता का नवीनीकरण संभव नहीं है। कृपया कार्यालय से संपर्क करें।');
        }

        $payment = $payments->createForMember($member, 'renewal');
        $audit->log('membership.renewal_initiated', $member, [], ['order_no' => $payment->order_no]);

        return redirect($this->signedPaymentUrl($payment))
            ->with('success', 'नवीनीकरण हेतु भुगतान पूरा करें।');
    }

    public function payment(MembershipPayment $payment)
    {
        $payment->load('member');

        abort_unless(in_array($payment->status, ['pending', 'failed'], true), 404);

        $member = $payment->member;

        return view('membership.payment', [
            'payment' => $payment,
            'member' => $member,
            'gateway' => config('maku.payment.gateway'),
            'cashfreeSessionId' => data_get($payment->payload, 'cashfree_payment_session_id'),
            'cashfreeEnv' => config('maku.payment.cashfree.env', 'sandbox'),
            'sandboxAction' => URL::temporarySignedRoute(
                'membership.payment.sandbox',
                now()->addHour(),
                ['payment' => $payment->order_no]
            ),
            'utrAction' => URL::temporarySignedRoute(
                'membership.payment.utr',
                now()->addHour(),
                ['payment' => $payment->order_no]
            ),
            'cashAction' => URL::temporarySignedRoute(
                'membership.payment.cash',
                now()->addHour(),
                ['payment' => $payment->order_no]
            ),
            'receiptAction' => URL::temporarySignedRoute(
                'membership.payment.receipt',
                now()->addHour(),
                ['payment' => $payment->order_no]
            ),
            'cashRequested' => $payment->gateway === 'cash' || filled(data_get($payment->payload, 'cash_requested_at')),
            'receiptUploaded' => filled($payment->receipt_path),
            'feeAmount' => (float) $payment->fee_amount,
            'taxAmount' => (float) $payment->tax_amount,
            'totalAmount' => $payment->totalAmount(),
            'feePercent' => (float) setting('gateway_fee_percent', config('maku.payment.fee_percent', 2)),
            'gstPercent' => (float) setting('gateway_gst_percent', config('maku.payment.gst_percent', 18)),
        ]);
    }

    public function simulateSandbox(MembershipPayment $payment, PaymentService $payments, AuditLogger $audit)
    {
        abort_unless(gateway_is_sandbox() && ! app()->environment('production'), 404);

        $payments->markPaid($payment, 'SANDBOX-'.strtoupper(uniqid()), ['gateway' => 'sandbox']);
        $audit->log('payment.paid', $payment, ['status' => 'pending'], ['status' => 'paid', 'gateway' => 'sandbox']);

        return redirect($this->signedReceiptUrl($payment->refresh()))
            ->with('success', 'परीक्षण भुगतान सफल रहा। आपकी सदस्यता सक्रिय कर दी गई है।');
    }

    public function cash(MembershipPayment $payment, PaymentService $payments, AuditLogger $audit)
    {
        abort_unless($payment->status === 'pending', 404);

        $payment = $payments->useOfflineAmount($payment);

        $payment->update([
            'gateway' => 'cash',
            'payload' => array_merge($payment->payload ?? [], [
                'cash_requested_at' => now()->toIso8601String(),
            ]),
        ]);

        $audit->log('payment.cash_requested', $payment, [], ['status' => 'pending', 'method' => 'cash']);

        return redirect($this->signedReceiptUrl($payment->refresh()))
            ->with('success', 'आपका नकद भुगतान अनुरोध दर्ज हो गया है। प्रशासक द्वारा स्वीकृति के बाद सदस्यता सक्रिय होगी।');
    }

    public function uploadReceipt(Request $request, MembershipPayment $payment, PaymentService $payments, AuditLogger $audit)
    {
        abort_unless($payment->status === 'pending', 404);

        $data = $request->validate([
            'medium' => ['required', 'in:upi,bank'],
            'receipt' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,pdf', 'max:'.(int) config('maku.uploads.max_kb', 4096)],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'medium.required' => 'कृपया भुगतान का माध्यम चुनें।',
            'medium.in' => 'कृपया सही भुगतान माध्यम चुनें।',
            'receipt.required' => 'कृपया भुगतान रसीद अपलोड करें।',
            'receipt.mimes' => 'रसीद JPG, PNG, WEBP या PDF में होनी चाहिए।',
            'receipt.max' => 'रसीद का आकार 4 MB से अधिक नहीं होना चाहिए।',
        ]);

        $disk = Storage::disk(config('maku.uploads.disk'));

        if ($payment->receipt_path) {
            $disk->delete($payment->receipt_path);
        }

        $path = $request->file('receipt')->store('payments/receipts', config('maku.uploads.disk'));

        $payment = $payments->useOfflineAmount($payment);

        $payment->update([
            'gateway' => 'online',
            'receipt_path' => $path,
            'payload' => array_merge($payment->payload ?? [], [
                'payment_medium' => $data['medium'],
                'payment_note' => $data['note'] ?? null,
                'receipt_uploaded_at' => now()->toIso8601String(),
            ]),
        ]);

        $audit->log('payment.receipt_uploaded', $payment, [], [
            'status' => 'pending',
            'medium' => $data['medium'],
        ]);

        return redirect($this->signedReceiptUrl($payment->refresh()))
            ->with('success', 'आपकी भुगतान रसीद अपलोड हो गई है। प्रशासक द्वारा सत्यापन के बाद सदस्यता सक्रिय होगी।');
    }

    public function submitUtr(Request $request, MembershipPayment $payment, AuditLogger $audit)
    {
        $data = $request->validate([
            'utr' => ['required', 'string', 'min:6', 'max:30', 'regex:/^[A-Za-z0-9]+$/'],
        ], [
            'utr.required' => 'कृपया UTR / संदर्भ संख्या भरें।',
            'utr.regex' => 'UTR में केवल अक्षर और अंक होने चाहिए।',
        ]);

        $payment->update(['utr' => strtoupper($data['utr']), 'gateway' => 'manual']);
        $audit->log('payment.utr_submitted', $payment, [], ['utr' => $payment->utr]);

        return redirect($this->signedReceiptUrl($payment))
            ->with('success', 'आपका UTR दर्ज कर लिया गया है। कार्यालय द्वारा सत्यापन के बाद सदस्यता सक्रिय होगी।');
    }

    public function cashfreeReturn(Request $request, MembershipPayment $payment, PaymentService $payments, AuditLogger $audit)
    {
        $orderId = (string) ($request->query('order_id') ?: $payment->gateway_order_id);

        abort_if($orderId === '', 404);

        abort_unless(
            in_array($orderId, array_filter([$payment->order_no, $payment->gateway_order_id]), true),
            403,
            'भुगतान ऑर्डर मेल नहीं खाता।'
        );

        $order = $payments->fetchCashfreeOrder($orderId);

        if (! $order) {
            return redirect($this->signedPaymentUrl($payment))
                ->with('error', 'Cashfree से भुगतान स्थिति प्राप्त नहीं हो सकी। कृपया कुछ समय बाद पुनः प्रयास करें।');
        }

        if (($order['order_status'] ?? '') !== 'PAID') {
            return redirect($this->signedPaymentUrl($payment))
                ->with('error', 'भुगतान अभी पूर्ण नहीं हुआ है। स्थिति: '.($order['order_status'] ?? 'अज्ञात'));
        }

        $wasPaid = $payment->status !== 'paid';
        $payments->markPaid($payment, (string) ($order['cf_order_id'] ?? $orderId), $order);
        $audit->log('payment.cashfree_return', $payment, ['status' => $wasPaid ? 'pending' : 'paid'], ['status' => 'paid']);

        return redirect($this->signedReceiptUrl($payment->refresh()))
            ->with('success', 'भुगतान सफल! आपकी सदस्यता सक्रिय कर दी गई है।');
    }

    public function receipt(MembershipPayment $payment)
    {
        $payment->load('member.category');

        return view('membership.receipt', [
            'payment' => $payment,
            'member' => $payment->member,
        ]);
    }

    public function failed(MembershipPayment $payment)
    {
        $payment->update(['status' => 'failed']);

        return redirect($this->signedPaymentUrl($payment))
            ->with('error', 'भुगतान पूरा नहीं हो सका। कृपया पुनः प्रयास करें या कार्यालय से संपर्क करें।');
    }

    private function signedPaymentUrl(MembershipPayment $payment): string
    {
        return URL::temporarySignedRoute(
            'membership.payment',
            now()->addHours(2),
            ['payment' => $payment->order_no]
        );
    }

    private function signedReceiptUrl(MembershipPayment $payment): string
    {
        return URL::temporarySignedRoute(
            'membership.receipt',
            now()->addHours(2),
            ['payment' => $payment->order_no]
        );
    }
}
