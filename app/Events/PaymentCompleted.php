<?php

namespace App\Events;

use App\Models\MembershipPayment;
use Illuminate\Foundation\Events\Dispatchable;

class PaymentCompleted
{
    use Dispatchable;

    public function __construct(public MembershipPayment $payment) {}
}
