<?php

namespace App\Http\Controllers;

use App\Models\MembershipPayment;
use App\Services\AuditLogger;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    public function cashfree(Request $request, PaymentService $payments, AuditLogger $audit)
    {
        $body = $request->getContent();
        $timestamp = (string) $request->header('x-webhook-timestamp', '');
        $signature = (string) $request->header('x-webhook-signature', '');

        if (! $payments->verifyCashfreeWebhookSignature($body, $timestamp, $signature)) {
            Log::warning('Cashfree webhook signature mismatch', ['ip' => $request->ip()]);

            return response()->json(['status' => 'invalid-signature'], 400);
        }

        $order = (array) data_get(json_decode($body, true), 'data.order', []);
        $orderId = (string) ($order['order_id'] ?? '');

        if ($orderId === '' || ($order['order_status'] ?? '') !== 'PAID') {
            return response()->json(['status' => 'ignored']);
        }

        $payment = MembershipPayment::query()
            ->where(fn ($q) => $q->where('gateway_order_id', $orderId)->orWhere('order_no', $orderId))
            ->first();

        if (! $payment) {
            Log::warning('Cashfree webhook payment not found', ['order_id' => $orderId]);

            return response()->json(['status' => 'not-found'], 404);
        }

        $payments->markPaid($payment, (string) ($order['cf_order_id'] ?? $orderId), $order);
        $audit->log('payment.cashfree_webhook', $payment, [], ['status' => 'paid', 'order_id' => $orderId]);

        return response()->json(['status' => 'ok']);
    }
}
