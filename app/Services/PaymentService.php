<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Events\PaymentCompleted;
use App\Models\Member;
use App\Models\MembershipPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class PaymentService
{
    public function createForMember(Member $member, string $type): MembershipPayment
    {
        $amount = $type === 'renewal'
            ? (float) setting('renewal_fee', config('maku.membership.renewal_fee'))
            : (float) setting('membership_fee', config('maku.membership.fee'));

        $breakdown = $this->feeBreakdown($amount);

        $payment = MembershipPayment::create([
            'member_id' => $member->id,
            'order_no' => 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
            'type' => $type,
            'amount' => $amount,
            'fee_amount' => $breakdown['fee'],
            'tax_amount' => $breakdown['tax'],
            'total_amount' => $breakdown['total'],
            'currency' => config('maku.payment.currency', 'INR'),
            'gateway' => config('maku.payment.gateway', 'sandbox'),
            'status' => PaymentStatus::Pending->value,
        ]);

        try {
            match (config('maku.payment.gateway')) {
                'cashfree' => $this->createCashfreeOrder($payment),
                default => null,
            };
        } catch (\Throwable $e) {
            Log::error('Payment gateway order creation failed', [
                'gateway' => config('maku.payment.gateway'),
                'order_no' => $payment->order_no,
                'error' => $e->getMessage(),
            ]);
        }

        return $payment->refresh();
    }

    public function markPaid(MembershipPayment $payment, ?string $gatewayPaymentId = null, array $payload = []): MembershipPayment
    {
        $wasPaid = false;

        $payment = DB::transaction(function () use ($payment, $gatewayPaymentId, $payload, &$wasPaid) {
            $locked = MembershipPayment::query()->lockForUpdate()->findOrFail($payment->getKey());

            if ($locked->status === PaymentStatus::Paid->value) {
                return $locked;
            }

            $locked->update([
                'status' => PaymentStatus::Paid->value,
                'gateway_payment_id' => $gatewayPaymentId ?: $locked->gateway_payment_id,
                'receipt_no' => $locked->receipt_no ?: 'RCPT-'.now()->year.'-'.str_pad((string) $locked->id, 6, '0', STR_PAD_LEFT),
                'paid_at' => now(),
                'payload' => $payload ?: $locked->payload,
            ]);

            $wasPaid = true;

            return $locked;
        });

        if ($wasPaid) {
            PaymentCompleted::dispatch($payment);
        }

        return $payment;
    }

    /**
     * @return array{base: float, fee: float, tax: float, total: float, fee_percent: float, gst_percent: float}
     */
    public function feeBreakdown(float $base): array
    {
        $base = round(max(0, $base), 2);
        $feePercent = max(0, (float) setting('gateway_fee_percent', config('maku.payment.fee_percent', 2)));
        $gstPercent = max(0, (float) setting('gateway_gst_percent', config('maku.payment.gst_percent', 18)));

        $fee = round($base * $feePercent / 100, 2);
        $tax = round($fee * $gstPercent / 100, 2);

        return [
            'base' => $base,
            'fee' => $fee,
            'tax' => $tax,
            'total' => round($base + $fee + $tax, 2),
            'fee_percent' => $feePercent,
            'gst_percent' => $gstPercent,
        ];
    }

    public function useOfflineAmount(MembershipPayment $payment): MembershipPayment
    {
        $payment->update([
            'fee_amount' => 0,
            'tax_amount' => 0,
            'total_amount' => $payment->amount,
        ]);

        return $payment;
    }

    public function cashfreeConfigured(): bool
    {
        return filled(config('maku.payment.cashfree.app_id'))
            && filled(config('maku.payment.cashfree.secret_key'));
    }

    public function cashfreeBaseUrl(): string
    {
        return config('maku.payment.cashfree.env') === 'production'
            ? 'https://api.cashfree.com'
            : 'https://sandbox.cashfree.com';
    }

    private function createCashfreeOrder(MembershipPayment $payment): void
    {
        if (! $this->cashfreeConfigured()) {
            throw new RuntimeException('Cashfree कुंजियाँ कॉन्फ़िगर नहीं हैं। कृपया एडमिन पैनल → सेटिंग्स → भुगतान में App ID और Secret Key भरें।');
        }

        $payment->loadMissing('member');
        $member = $payment->member;

        $response = Http::withHeaders($this->cashfreeHeaders())
            ->timeout(20)
            ->post($this->cashfreeBaseUrl().'/pg/orders', [
                'order_id' => $payment->order_no,
                'order_amount' => round($payment->totalAmount(), 2),
                'order_currency' => $payment->currency,
                'customer_details' => [
                    'customer_id' => 'MAKU-'.$member->id,
                    'customer_name' => $member->name,
                    'customer_email' => $member->email ?: 'member-'.$member->id.'@maku.local',
                    'customer_phone' => $member->phone,
                ],
                'order_meta' => [
                    'return_url' => route('membership.payment.cashfree.return', ['payment' => $payment->order_no]).'?order_id={order_id}',
                ],
                'order_note' => $payment->type === 'renewal' ? 'MAKU सदस्यता नवीनीकरण' : 'MAKU सदस्यता शुल्क',
            ]);

        if ($response->failed()) {
            Log::error('Cashfree order creation failed', ['status' => $response->status(), 'body' => $response->json()]);
            throw new RuntimeException('Cashfree भुगतान ऑर्डर बनाने में समस्या हुई। कृपया बाद में प्रयास करें।');
        }

        $payment->update([
            'gateway_order_id' => $response->json('order_id') ?: $payment->order_no,
            'payload' => array_merge($payment->payload ?? [], [
                'cashfree_payment_session_id' => $response->json('payment_session_id'),
            ]),
        ]);
    }

    public function fetchCashfreeOrder(string $orderId): ?array
    {
        if (! $this->cashfreeConfigured()) {
            return null;
        }

        $response = Http::withHeaders($this->cashfreeHeaders())
            ->timeout(20)
            ->get($this->cashfreeBaseUrl().'/pg/orders/'.urlencode($orderId));

        if ($response->failed()) {
            Log::warning('Cashfree order fetch failed', ['order_id' => $orderId, 'status' => $response->status()]);

            return null;
        }

        return $response->json();
    }

    public function verifyCashfreeWebhookSignature(string $body, string $timestamp, string $signature): bool
    {
        $secret = (string) config('maku.payment.cashfree.secret_key');

        if ($secret === '' || $timestamp === '' || $signature === '') {
            return false;
        }

        $expected = base64_encode(hash_hmac('sha256', $timestamp.$body, $secret, true));

        return hash_equals($expected, $signature);
    }

    private function cashfreeHeaders(): array
    {
        return [
            'x-client-id' => (string) config('maku.payment.cashfree.app_id'),
            'x-client-secret' => (string) config('maku.payment.cashfree.secret_key'),
            'x-api-version' => (string) config('maku.payment.cashfree.api_version', '2023-08-01'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }
}
