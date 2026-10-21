<?php

namespace App\Listeners;

use App\Events\PaymentCompleted;
use App\Services\MembershipService;

class ActivateMembershipAfterPayment
{
    public function __construct(private MembershipService $memberships) {}

    public function handle(PaymentCompleted $event): void
    {
        $payment = $event->payment->fresh();

        if (! $payment || $payment->status !== 'paid' || ! $payment->member) {
            return;
        }

        $this->memberships->activate($payment->member, $payment);
    }
}
