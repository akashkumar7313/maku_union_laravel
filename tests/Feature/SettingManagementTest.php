<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_payment_and_mail_settings(): void
    {
        $this->seed();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'payment_gateway' => 'cashfree',
            'cashfree_app_id' => 'TEST-APP-ID',
            'cashfree_secret_key' => 'test-secret-key',
            'cashfree_env' => 'sandbox',
            'cashfree_api_version' => '2023-08-01',
            'mail_mailer' => 'smtp',
            'mail_host' => 'smtp.example.com',
            'mail_port' => 587,
            'mail_username' => 'mailer@example.com',
            'mail_password' => 'mail-password',
            'mail_encryption' => 'tls',
            'mail_from_address' => 'noreply@example.com',
            'mail_from_name' => 'MAKU Union',
            'contact_phone' => '98765 43210',
            'grievance_helpline' => '+91 91234 56789',
        ])->assertRedirect();

        $this->assertSame('cashfree', Setting::get('payment_gateway'));
        $this->assertSame('TEST-APP-ID', Setting::get('cashfree_app_id'));
        $this->assertSame('test-secret-key', Setting::get('cashfree_secret_key'));
        $this->assertSame('smtp', Setting::get('mail_mailer'));
        $this->assertSame('smtp.example.com', Setting::get('mail_host'));
        $this->assertSame('+919876543210', Setting::get('contact_phone'));
        $this->assertSame('+919123456789', Setting::get('grievance_helpline'));
    }

    public function test_blank_secret_fields_do_not_erase_saved_keys(): void
    {
        $this->seed();

        $admin = User::factory()->admin()->create();

        Setting::put('cashfree_secret_key', 'keep-me', 'payment');
        Setting::put('mail_password', 'keep-this-too', 'mail');

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'cashfree_app_id' => 'TEST-APP-ID',
            'cashfree_secret_key' => '',
            'mail_username' => 'mailer@example.com',
            'mail_password' => '',
        ])->assertRedirect();

        $this->assertSame('keep-me', Setting::get('cashfree_secret_key'));
        $this->assertSame('keep-this-too', Setting::get('mail_password'));
    }

    public function test_admin_can_send_a_test_mail(): void
    {
        $this->seed();

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post(route('admin.settings.test-mail'), [
            'test_email' => 'test@example.com',
        ])->assertRedirect();

        $messages = app('mailer')->getSymfonyTransport()->messages();

        $this->assertCount(1, $messages);
        $this->assertSame('test@example.com', $messages[0]->getEnvelope()->getRecipients()[0]->getAddress());
    }
}
