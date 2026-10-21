<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MembershipPayment;
use App\Models\User;
use App\Models\WorkerCategory;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_can_recover_membership_number_with_aadhaar_and_phone(): void
    {
        $this->seed();

        $member = Member::query()->firstOrFail();

        $response = $this->post(route('membership.forgot.submit'), [
            'aadhaar_number' => $member->aadhaar_number,
            'phone' => $member->phone,
        ]);

        $response->assertRedirect();

        $this->get($response->headers->get('Location'))
            ->assertOk()
            ->assertSee($member->membership_no);
    }

    public function test_membership_recovery_fails_with_wrong_aadhaar(): void
    {
        $this->seed();

        $member = Member::query()->firstOrFail();

        $this->post(route('membership.forgot.submit'), [
            'aadhaar_number' => '123456789012',
            'phone' => $member->phone,
        ])->assertSessionHasErrors('aadhaar_number');
    }

    public function test_registration_stores_full_aadhaar_and_creates_fee_breakdown(): void
    {
        $this->seed();

        $category = WorkerCategory::query()->where('is_active', true)->firstOrFail();

        $this->post(route('membership.store'), [
            'name' => 'आधार परीक्षण',
            'guardian_name' => 'परीक्षण',
            'phone' => '9000000001',
            'address' => 'ग्राम परीक्षण, मिर्जापुर',
            'district' => 'मिर्जापुर',
            'block' => 'सदर',
            'worker_category_id' => $category->id,
            'aadhaar_number' => '1234 1234 1234',
            'terms' => '1',
        ])->assertRedirect();

        $member = Member::query()->where('phone', '9000000001')->firstOrFail();

        $this->assertSame('123412341234', $member->aadhaar_number);
        $this->assertSame('1234', $member->aadhaar_last4);
        $this->assertNotNull($member->aadhaar_hash);

        $payment = MembershipPayment::query()->where('member_id', $member->id)->firstOrFail();

        $this->assertSame(100.0, (float) $payment->amount);
        $this->assertSame(102.36, $payment->totalAmount());

        $this->post(route('membership.forgot.submit'), [
            'aadhaar_number' => '123412341234',
            'phone' => '9000000001',
        ])->assertRedirect();
    }

    public function test_online_payment_adds_gateway_fee_and_gst_while_net_fee_stays_100(): void
    {
        $this->seed();

        $breakdown = app(PaymentService::class)->feeBreakdown(100.0);

        $this->assertSame(100.0, $breakdown['base']);
        $this->assertSame(2.0, $breakdown['fee']);
        $this->assertSame(0.36, $breakdown['tax']);
        $this->assertSame(102.36, $breakdown['total']);
        $this->assertSame(100.0, round($breakdown['total'] - $breakdown['fee'] - $breakdown['tax'], 2));

        $other = app(PaymentService::class)->feeBreakdown(250.0);

        $this->assertSame(5.0, $other['fee']);
        $this->assertSame(0.9, $other['tax']);
        $this->assertSame(255.9, $other['total']);
    }

    public function test_payment_page_shows_fee_breakdown_and_net_100(): void
    {
        $this->seed();

        $member = Member::query()->firstOrFail();
        $payment = app(PaymentService::class)->createForMember($member, 'new');

        $this->assertSame(102.36, $payment->totalAmount());

        $url = URL::temporarySignedRoute('membership.payment', now()->addHour(), [
            'payment' => $payment->order_no,
        ]);

        $this->get($url)
            ->assertOk()
            ->assertSee('102.36')
            ->assertSee('2.00')
            ->assertSee('0.36');
    }

    public function test_online_receipt_upload_stays_pending_for_admin_approval(): void
    {
        $this->seed();

        Storage::fake(config('maku.uploads.disk'));

        $payment = MembershipPayment::query()->where('status', 'pending')->firstOrFail();

        $url = URL::temporarySignedRoute('membership.payment.receipt', now()->addHour(), [
            'payment' => $payment->order_no,
        ]);

        $this->post($url, [
            'medium' => 'upi',
            'receipt' => UploadedFile::fake()->image('receipt.jpg'),
            'note' => 'UTR123456',
        ])->assertRedirect();

        $payment->refresh();

        $this->assertSame('pending', $payment->status);
        $this->assertSame('online', $payment->gateway);
        $this->assertSame('upi', data_get($payment->payload, 'payment_medium'));
        $this->assertSame(100.0, (float) $payment->total_amount);
        $this->assertSame(0.0, (float) $payment->fee_amount);
        $this->assertNotNull($payment->receipt_path);
        Storage::disk(config('maku.uploads.disk'))->assertExists($payment->receipt_path);
    }

    public function test_cash_payment_stays_pending_until_admin_approval(): void
    {
        $this->seed();

        $payment = MembershipPayment::query()->where('status', 'pending')->firstOrFail();

        $url = URL::temporarySignedRoute('membership.payment.cash', now()->addHour(), [
            'payment' => $payment->order_no,
        ]);

        $this->post($url)->assertRedirect();

        $payment->refresh();

        $this->assertSame('pending', $payment->status);
        $this->assertSame('cash', $payment->gateway);
        $this->assertSame(100.0, (float) $payment->total_amount);
        $this->assertNotNull(data_get($payment->payload, 'cash_requested_at'));

        $admin = User::query()->where('role', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('admin.payments.verify', $payment))
            ->assertRedirect();

        $this->assertSame('paid', $payment->refresh()->status);
    }
}
