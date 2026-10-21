<?php

namespace App\Services;

use App\Enums\MemberStatus;
use App\Models\Member;
use App\Models\MembershipPayment;
use App\Models\MembershipRenewal;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

class MembershipService
{
    public function register(array $data, ?User $actor = null): Member
    {
        return DB::transaction(function () use ($data, $actor) {
            $photo = $data['photo'] ?? null;
            $idDocument = $data['id_document'] ?? null;
            unset($data['photo'], $data['id_document'], $data['terms'], $data['website'], $data['aadhaar_last4']);

            $member = new Member($data);
            $member->membership_no = 'TMP-'.Str::uuid();
            $member->joined_on = today();
            $member->status = MemberStatus::Pending->value;
            $member->created_by = $actor?->id;

            if ($photo instanceof UploadedFile) {
                $member->photo = $photo->store('members/photos', config('maku.uploads.disk'));
            }

            if ($idDocument instanceof UploadedFile) {
                $member->id_document = $idDocument->store('members/documents', config('maku.uploads.disk'));
            }

            $member->save();
            $member->membership_no = $this->membershipNumber($member);
            $member->save();

            return $member;
        });
    }

    public function membershipNumber(Member $member): string
    {
        return sprintf(
            '%s-%s-%05d',
            config('maku.membership.prefix'),
            $member->joined_on?->year ?? now()->year,
            $member->getKey()
        );
    }

    /**
     * Called when a payment is marked successful. Idempotent.
     */
    public function activate(Member $member, MembershipPayment $payment): Member
    {
        return DB::transaction(function () use ($member, $payment) {
            $member = Member::query()->lockForUpdate()->findOrFail($member->getKey());
            $years = (int) config('maku.membership.duration_years', 1);

            if ($payment->type === 'renewal') {
                $start = $member->valid_until && $member->valid_until->isFuture()
                    ? $member->valid_until->copy()
                    : today();
            } else {
                $start = today();
            }

            $end = $start->copy()->addYears($years);

            $member->valid_from = $member->valid_from && $member->valid_from->lt($start)
                ? $member->valid_from
                : $start;
            $member->valid_until = $end;
            $member->status = MemberStatus::Active->value;
            $member->save();

            MembershipRenewal::create([
                'member_id' => $member->id,
                'payment_id' => $payment->id,
                'valid_from' => $start,
                'valid_until' => $end,
                'amount' => $payment->amount,
                'created_by' => $member->created_by,
            ]);

            return $member;
        });
    }

    public function suspend(Member $member, string $reason, ?User $actor = null): void
    {
        $member->update([
            'status' => MemberStatus::Suspended->value,
            'notes' => trim(($member->notes ? $member->notes."\n" : '').'निलंबन: '.$reason),
        ]);
    }

    public function markExpired(): int
    {
        return Member::query()
            ->where('status', MemberStatus::Active->value)
            ->whereNotNull('valid_until')
            ->whereDate('valid_until', '<', today())
            ->update(['status' => MemberStatus::Expired->value]);
    }

    public function findByCredentials(string $membershipNo, string $phone): ?Member
    {
        $digits = clean_digits($phone);

        if ($digits === '' || $membershipNo === '') {
            return null;
        }

        return Member::query()
            ->where('membership_no', trim($membershipNo))
            ->whereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+91', '') = ?", [
                ltrim($digits, '0'),
            ])
            ->first();
    }

    public function findByAadhaarAndPhone(string $aadhaar, string $phone): ?Member
    {
        $aadhaar = clean_digits($aadhaar);
        $digits = ltrim(clean_digits($phone), '0');

        if (strlen($aadhaar) !== 12 || $digits === '') {
            return null;
        }

        $hash = hash_hmac('sha256', $aadhaar, (string) config('app.key'));

        return Member::query()
            ->where('aadhaar_hash', $hash)
            ->whereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+91', '') = ?", [$digits])
            ->latest('id')
            ->first();
    }

    public function updateStatus(Member $member, MemberStatus $status, ?User $actor = null): void
    {
        if ($status === MemberStatus::Active && ! $member->valid_until) {
            throw new InvalidArgumentException('सक्रिय करने के लिए सदस्यता की अवधि आवश्यक है।');
        }

        $member->update(['status' => $status->value]);
    }
}
