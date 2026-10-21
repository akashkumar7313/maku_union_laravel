<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'member_id', 'order_no', 'receipt_no', 'type', 'amount', 'fee_amount', 'tax_amount', 'total_amount', 'currency', 'gateway',
    'gateway_order_id', 'gateway_payment_id', 'utr', 'receipt_path', 'status', 'paid_at', 'payload',
])]
class MembershipPayment extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'fee_amount' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'paid_at' => 'datetime',
            'payload' => 'array',
        ];
    }

    public function totalAmount(): float
    {
        $total = (float) $this->total_amount;

        return $total > 0 ? $total : (float) $this->amount;
    }

    public function hasOnlineFee(): bool
    {
        return (float) $this->fee_amount > 0 || (float) $this->tax_amount > 0;
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function statusLabel(): string
    {
        return PaymentStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function statusColor(): string
    {
        return PaymentStatus::tryFrom($this->status)?->color() ?? 'secondary';
    }
}
