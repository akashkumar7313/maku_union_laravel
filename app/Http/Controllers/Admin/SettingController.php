<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesUploads;
use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SettingController extends Controller
{
    use HandlesUploads;

    private const TEXT_FIELDS = [
        'site_name_hi' => 'general',
        'site_name_en' => 'general',
        'site_short_name' => 'general',
        'tagline_hi' => 'general',
        'affiliation_hi' => 'general',
        'registration_no' => 'general',
        'founded_year' => 'general',
        'site_description' => 'general',
        'site_keywords' => 'general',
        'footer_note' => 'general',
        'copyright_text' => 'general',
        'developer_credit' => 'general',
        'contact_address' => 'contact',
        'contact_phone' => 'contact',
        'contact_phone_alt' => 'contact',
        'contact_email' => 'contact',
        'contact_hours' => 'contact',
        'map_embed' => 'contact',
        'map_link' => 'contact',
        'grievance_helpline' => 'contact',
        'grievance_email' => 'contact',
        'grievance_sla_days' => 'contact',
        'grievance_note' => 'contact',
        'facebook_url' => 'social',
        'youtube_url' => 'social',
        'whatsapp_number' => 'social',
        'twitter_url' => 'social',
        'instagram_url' => 'social',
        'telegram_url' => 'social',
        'about_short' => 'about',
        'about_objective' => 'about',
        'about_mission' => 'about',
        'about_vision' => 'about',
        'membership_duration_text' => 'membership',
        'membership_card_note' => 'membership',
        'upi_id' => 'payment',
        'bank_name' => 'payment',
        'account_name' => 'payment',
        'account_number' => 'payment',
        'ifsc' => 'payment',
        'payment_instructions' => 'payment',
        'payment_gateway' => 'payment',
        'cashfree_app_id' => 'payment',
        'cashfree_secret_key' => 'payment',
        'cashfree_env' => 'payment',
        'cashfree_api_version' => 'payment',
        'mail_mailer' => 'mail',
        'mail_host' => 'mail',
        'mail_port' => 'mail',
        'mail_username' => 'mail',
        'mail_password' => 'mail',
        'mail_encryption' => 'mail',
        'mail_from_address' => 'mail',
        'mail_from_name' => 'mail',
        'meta_title' => 'seo',
        'meta_description' => 'seo',
        'meta_keywords' => 'seo',
        'google_analytics_id' => 'seo',
        'search_console_id' => 'seo',
    ];

    private const SECRET_FIELDS = [
        'cashfree_secret_key',
        'mail_password',
    ];

    private const PHONE_FIELDS = [
        'contact_phone',
        'contact_phone_alt',
        'grievance_helpline',
        'whatsapp_number',
    ];

    private const UPLOAD_FIELDS = [
        'logo' => 'site_logo',
        'favicon' => 'site_favicon',
        'about_image' => 'about_image',
        'payment_qr' => 'payment_qr',
        'og_image' => 'og_image',
    ];

    public function edit()
    {
        return view('admin.settings.edit', [
            'settings' => Setting::allCached(),
        ]);
    }

    public function update(Request $request, AuditLogger $audit)
    {
        $data = $request->validate([
            'site_name_hi' => ['nullable', 'string', 'max:200'],
            'site_name_en' => ['nullable', 'string', 'max:200'],
            'site_short_name' => ['nullable', 'string', 'max:50'],
            'tagline_hi' => ['nullable', 'string', 'max:300'],
            'affiliation_hi' => ['nullable', 'string', 'max:200'],
            'registration_no' => ['nullable', 'string', 'max:100'],
            'founded_year' => ['nullable', 'digits:4'],
            'site_description' => ['nullable', 'string', 'max:500'],
            'site_keywords' => ['nullable', 'string', 'max:500'],
            'footer_note' => ['nullable', 'string', 'max:500'],
            'copyright_text' => ['nullable', 'string', 'max:200'],
            'developer_credit' => ['nullable', 'string', 'max:200'],
            'contact_address' => ['nullable', 'string', 'max:500'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'contact_phone_alt' => ['nullable', 'string', 'max:30'],
            'contact_email' => ['nullable', 'email', 'max:190'],
            'contact_hours' => ['nullable', 'string', 'max:150'],
            'map_embed' => ['nullable', 'string', 'max:3000'],
            'map_link' => ['nullable', 'url', 'max:255'],
            'grievance_helpline' => ['nullable', 'string', 'max:30'],
            'grievance_email' => ['nullable', 'email', 'max:190'],
            'grievance_sla_days' => ['nullable', 'integer', 'min:0', 'max:365'],
            'grievance_note' => ['nullable', 'string', 'max:1000'],
            'facebook_url' => ['nullable', 'url', 'max:255'],
            'youtube_url' => ['nullable', 'url', 'max:255'],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'twitter_url' => ['nullable', 'url', 'max:255'],
            'instagram_url' => ['nullable', 'url', 'max:255'],
            'telegram_url' => ['nullable', 'url', 'max:255'],
            'about_short' => ['nullable', 'string', 'max:2000'],
            'about_objective' => ['nullable', 'string', 'max:2000'],
            'about_mission' => ['nullable', 'string', 'max:2000'],
            'about_vision' => ['nullable', 'string', 'max:2000'],
            'membership_fee' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'renewal_fee' => ['nullable', 'numeric', 'min:0', 'max:100000'],
            'membership_duration_text' => ['nullable', 'string', 'max:60'],
            'membership_card_note' => ['nullable', 'string', 'max:500'],
            'upi_id' => ['nullable', 'string', 'max:100'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'account_name' => ['nullable', 'string', 'max:150'],
            'account_number' => ['nullable', 'string', 'max:40'],
            'ifsc' => ['nullable', 'string', 'max:20'],
            'payment_instructions' => ['nullable', 'string', 'max:1000'],
            'payment_gateway' => ['nullable', 'in:sandbox,cashfree,manual'],
            'gateway_fee_percent' => ['nullable', 'numeric', 'min:0', 'max:10'],
            'gateway_gst_percent' => ['nullable', 'numeric', 'min:0', 'max:28'],
            'cashfree_app_id' => ['nullable', 'string', 'max:120'],
            'cashfree_secret_key' => ['nullable', 'string', 'max:255'],
            'cashfree_env' => ['nullable', 'in:sandbox,production'],
            'cashfree_api_version' => ['nullable', 'string', 'max:30'],
            'mail_mailer' => ['nullable', 'in:smtp,log,sendmail'],
            'mail_host' => ['nullable', 'string', 'max:190'],
            'mail_port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mail_username' => ['nullable', 'string', 'max:190'],
            'mail_password' => ['nullable', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'in:tls,ssl,none'],
            'mail_from_address' => ['nullable', 'email', 'max:190'],
            'mail_from_name' => ['nullable', 'string', 'max:150'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'meta_keywords' => ['nullable', 'string', 'max:500'],
            'google_analytics_id' => ['nullable', 'string', 'max:40'],
            'search_console_id' => ['nullable', 'string', 'max:120'],
            'membership_open' => ['nullable', 'boolean'],
            'translate_enabled' => ['nullable', 'boolean'],
            'translate_kill_switch' => ['nullable', 'boolean'],
            'notice_enabled' => ['nullable', 'boolean'],
            'gallery_home_enabled' => ['nullable', 'boolean'],
            'logo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico,jpg,jpeg,webp,svg', 'max:512'],
            'about_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'payment_qr' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'og_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'remove_logo' => ['nullable', 'boolean'],
            'remove_favicon' => ['nullable', 'boolean'],
            'remove_about_image' => ['nullable', 'boolean'],
            'remove_payment_qr' => ['nullable', 'boolean'],
            'remove_og_image' => ['nullable', 'boolean'],
        ], [
            'contact_email.email' => 'मान्य संपर्क ईमेल भरें।',
            'grievance_email.email' => 'मान्य शिकायत ईमेल भरें।',
            'facebook_url.url' => 'मान्य Facebook लिंक भरें।',
            'youtube_url.url' => 'मान्य YouTube लिंक भरें।',
            'map_link.url' => 'मान्य मैप लिंक भरें।',
            'membership_fee.numeric' => 'सदस्यता शुल्क संख्या में होना चाहिए।',
            'payment_gateway.in' => 'भुगतान गेटवे का चयन सही नहीं है।',
            'gateway_fee_percent.numeric' => 'गेटवे शुल्क प्रतिशत संख्या में होना चाहिए।',
            'gateway_gst_percent.numeric' => 'GST प्रतिशत संख्या में होना चाहिए।',
            'cashfree_env.in' => 'Cashfree वातावरण sandbox या production ही हो सकता है।',
            'mail_mailer.in' => 'मेल वाहक smtp, log या sendmail ही हो सकता है।',
            'mail_encryption.in' => 'एन्क्रिप्शन tls, ssl या none ही हो सकता है।',
            'mail_from_address.email' => 'मान्य प्रेषक ईमेल भरें।',
            'logo.mimes' => 'लोगो JPG, PNG, WEBP या SVG में होना चाहिए।',
        ]);

        foreach (self::PHONE_FIELDS as $key) {
            if (array_key_exists($key, $data) && filled($data[$key])) {
                $data[$key] = phone_display($data[$key]);
            }
        }

        foreach (self::SECRET_FIELDS as $key) {
            if (array_key_exists($key, $data) && blank($data[$key])) {
                unset($data[$key]);
            }
        }

        $before = Setting::allCached();

        foreach (self::TEXT_FIELDS as $key => $group) {
            if (array_key_exists($key, $data)) {
                Setting::put($key, $data[$key], $group);
            }
        }

        foreach (['membership_fee', 'renewal_fee'] as $key) {
            if (array_key_exists($key, $data)) {
                Setting::put($key, $data[$key], 'membership');
            }
        }

        foreach (['gateway_fee_percent', 'gateway_gst_percent'] as $key) {
            if (array_key_exists($key, $data)) {
                Setting::put($key, $data[$key], 'payment');
            }
        }

        foreach (['membership_open', 'translate_enabled', 'translate_kill_switch', 'notice_enabled', 'gallery_home_enabled'] as $key) {
            Setting::put($key, $request->boolean($key) ? '1' : '0', 'flags');
        }

        foreach (self::UPLOAD_FIELDS as $field => $key) {
            $remove = $request->boolean('remove_'.$field);

            if ($remove) {
                $this->deleteUpload(Setting::get($key));
                Setting::put($key, '', 'branding');
            }

            if ($request->hasFile($field)) {
                $this->deleteUpload(Setting::get($key));
                Setting::put($key, $this->storeUpload($request->file($field), 'settings'), 'branding');
            }
        }

        Setting::flush();
        Cache::forget('maku.menu');

        $oldValues = [];
        $newValues = [];
        $keys = array_merge(array_keys(self::TEXT_FIELDS), ['membership_fee', 'renewal_fee', 'gateway_fee_percent', 'gateway_gst_percent', 'membership_open', 'translate_enabled', 'translate_kill_switch', 'notice_enabled', 'gallery_home_enabled'], array_values(self::UPLOAD_FIELDS));

        foreach ($keys as $key) {
            $old = $before->get($key);
            $new = Setting::get($key);

            if ((string) $old !== (string) $new) {
                $oldValues[$key] = $old;
                $newValues[$key] = $new;
            }
        }

        $audit->log('settings.updated', null, $oldValues, $newValues);

        return back()->with('success', 'सेटिंग्स सफलतापूर्वक सुरक्षित हो गईं।');
    }

    public function testMail(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'test_email' => ['required', 'email', 'max:190'],
        ], [
            'test_email.required' => 'परीक्षण हेतु ईमेल पता भरें।',
            'test_email.email' => 'मान्य ईमेल पता भरें।',
        ]);

        try {
            Mail::raw(
                "यह MAKU Union की मेल सेटिंग्स का परीक्षण संदेश है।\n\nयदि आपको यह संदेश मिला है तो मेल कॉन्फ़िगरेशन सही है।",
                function ($message) use ($data) {
                    $message->to($data['test_email'])->subject('MAKU Union मेल परीक्षण');
                }
            );
        } catch (\Throwable $e) {
            Log::error('Test mail failed', ['email' => $data['test_email'], 'error' => $e->getMessage()]);

            return back()->with('error', 'परीक्षण मेल नहीं भेजा जा सका: '.e($e->getMessage()));
        }

        return back()->with('success', 'परीक्षण मेल भेज दिया गया: '.e($data['test_email']));
    }
}
