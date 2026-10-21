<?php

namespace App\Models;

use App\Enums\MemberStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'membership_no', 'user_id', 'name', 'guardian_name', 'phone', 'email', 'gender', 'dob',
    'address', 'district', 'block', 'worker_category_id', 'photo', 'id_document',
    'aadhaar_last4', 'aadhaar_number', 'aadhaar_hash', 'joined_on', 'valid_from', 'valid_until', 'status', 'notes', 'created_by',
])]
class Member extends Model
{
    protected function casts(): array
    {
        return [
            'joined_on' => 'date',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'dob' => 'date',
            'aadhaar_number' => 'encrypted',
        ];
    }

    protected $hidden = ['aadhaar_number', 'aadhaar_hash'];

    protected static function booted(): void
    {
        static::saving(function (Member $member) {
            if (! $member->isDirty('aadhaar_number')) {
                return;
            }

            $digits = clean_digits((string) $member->aadhaar_number);

            $member->aadhaar_hash = $digits !== ''
                ? hash_hmac('sha256', $digits, (string) config('app.key'))
                : null;
            $member->aadhaar_last4 = $digits !== ''
                ? substr($digits, -4)
                : null;
            $member->aadhaar_number = $digits !== '' ? $digits : null;
        });
    }

    public function maskedAadhaar(): string
    {
        return $this->aadhaar_last4 ? '•••• •••• '.$this->aadhaar_last4 : '—';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(WorkerCategory::class, 'worker_category_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class);
    }

    public function renewals(): HasMany
    {
        return $this->hasMany(MembershipRenewal::class);
    }

    public function grievances(): HasMany
    {
        return $this->hasMany(Grievance::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', MemberStatus::Active->value)
            ->where(fn (Builder $q) => $q->whereNull('valid_until')->orWhere('valid_until', '>=', today()));
    }

    public function isActive(): bool
    {
        return $this->status === MemberStatus::Active->value
            && (! $this->valid_until || $this->valid_until->gte(today()));
    }

    public function statusLabel(): string
    {
        return MemberStatus::tryFrom($this->status)?->label() ?? $this->status;
    }

    public function statusColor(): string
    {
        return MemberStatus::tryFrom($this->status)?->color() ?? 'secondary';
    }

    public function photoUrl(): string
    {
        return $this->photo
            ? Storage::disk(config('maku.uploads.disk'))->url($this->photo)
            : asset('assets/img/avatar-placeholder.svg');
    }

    public function maskedPhone(): string
    {
        $phone = preg_replace('/\D+/', '', (string) $this->phone) ?? '';

        return strlen($phone) > 4 ? str_repeat('•', max(0, strlen($phone) - 4)).substr($phone, -4) : $phone;
    }
}
