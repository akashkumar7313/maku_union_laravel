<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['site_name_hi', 'मिर्जापुर असंगठित कामगार यूनियन', 'general'],
            ['site_name_en', 'Mirzapur Unorganised Workers Union', 'general'],
            ['site_short_name', 'MAKU Union', 'general'],
            ['tagline_hi', 'असंगठित एवं निर्माण श्रमिकों के अधिकारों के लिए समर्पित', 'general'],
            ['affiliation_hi', 'संबद्ध: भारतीय मजदूर संघ (BMS)', 'general'],
            ['registration_no', 'BMS/UP/2018/0142', 'general'],
            ['founded_year', '2018', 'general'],
            ['site_description', 'मिर्जापुर असंगठित कामगार यूनियन (MAKU) असंगठित एवं निर्माण श्रमिकों के अधिकारों, सामाजिक सुरक्षा और सम्मान के लिए कार्य करने वाला श्रमिक संगठन है।', 'general'],
            ['site_keywords', 'MAKU, मिर्जापुर, असंगठित कामगार, श्रमिक यूनियन, BMS, निर्माण श्रमिक, मनरेगा', 'general'],
            ['contact_address', 'मुख्य कार्यालय: MAKU भवन, स्टेशन रोड, मिर्जापुर, उत्तर प्रदेश - 231001', 'contact'],
            ['contact_phone', '+919876543210', 'contact'],
            ['contact_phone_alt', '+915422345678', 'contact'],
            ['contact_email', 'info@maku.local', 'contact'],
            ['contact_hours', 'सोमवार से शनिवार, प्रातः 10:00 से सायं 5:00 बजे तक', 'contact'],
            ['map_embed', '<iframe src="https://www.google.com/maps?q=MAKU+Union,+Station+Road,+Mirzapur,+Uttar+Pradesh+231001&output=embed" width="100%" height="320" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="MAKU Union कार्यालय मानचित्र"></iframe>', 'contact'],
            ['map_link', 'https://maps.google.com/?q=MAKU+Union+Station+Road+Mirzapur+Uttar+Pradesh+231001', 'contact'],
            ['grievance_helpline', '+919876543210', 'contact'],
            ['grievance_email', 'shikayat@maku.local', 'contact'],
            ['grievance_sla_days', '15', 'contact'],
            ['grievance_note', 'शिकायत दर्ज करने के बाद आपको शिकायत संख्या दी जाएगी। कृपया उसे सुरक्षित रखें। सामान्य शिकायतों का निस्तारण 15 कार्य दिवसों में किया जाता है।', 'contact'],
            ['facebook_url', 'https://www.facebook.com/MAKUUnion', 'social'],
            ['youtube_url', 'https://www.youtube.com/@MAKUUnion', 'social'],
            ['whatsapp_number', '+919876543210', 'social'],
            ['twitter_url', 'https://x.com/MAKUUnion', 'social'],
            ['instagram_url', 'https://www.instagram.com/MAKUUnion', 'social'],
            ['telegram_url', 'https://t.me/MAKUUnion', 'social'],
            ['about_short', 'मिर्जापुर असंगठित कामगार यूनियन (MAKU) असंगठित एवं निर्माण श्रमिकों के हितों की रक्षा, उनके अधिकारों के प्रति जागरूकता और सामाजिक सुरक्षा योजनाओं तक उनकी पहुँच सुनिश्चित करने के लिए कार्य करती है। संगठन श्रमिकों को सदस्यता, मार्गदर्शन, शिकायत निवारण और सामूहिक आवाज़ के माध्यम से सशक्त बनाता है।', 'about'],
            ['about_objective', 'प्रत्येक श्रमिक को उसके श्रम का सम्मान, न्यूनतम मजदूरी, सुरक्षित कार्यस्थल और सामाजिक सुरक्षा का अधिकार मिले — यही संगठन का मुख्य उद्देश्य है।', 'about'],
            ['about_mission', 'श्रमिकों को संगठित करना, उनकी समस्याओं को दर्ज करना, सरकारी योजनाओं की जानकारी देना और श्रम विभाग तथा प्रशासन तक उनकी आवाज़ पहुँचाना।', 'about'],
            ['about_vision', 'एक ऐसा समाज, जहाँ हर असंगठित श्रमिक सुरक्षित, सम्मानित और अपने अधिकारों के प्रति सजग हो।', 'about'],
            ['membership_fee', '100', 'membership'],
            ['renewal_fee', '100', 'membership'],
            ['gateway_fee_percent', '2', 'payment'],
            ['gateway_gst_percent', '18', 'payment'],
            ['membership_duration_text', 'एक वर्ष', 'membership'],
            ['membership_card_note', 'यह कार्ड संगठन का आधिकारिक सदस्यता प्रमाण है। कार्ड पर दी गई वैधता तिथि तक ही यह मान्य है।', 'membership'],
            ['upi_id', 'maku@upi', 'payment'],
            ['bank_name', 'भारतीय स्टेट बैंक', 'payment'],
            ['account_name', 'मिर्जापुर असंगठित कामगार यूनियन', 'payment'],
            ['account_number', '1234567890123', 'payment'],
            ['ifsc', 'SBIN0001234', 'payment'],
            ['payment_instructions', 'बैंक/UPI से भुगतान करने के बाद UTR या संदर्भ संख्या भुगतान पृष्ठ पर दर्ज करें। कार्यालय सत्यापन के बाद सदस्यता सक्रिय होगी।', 'payment'],
            ['payment_gateway', 'sandbox', 'payment'],
            ['cashfree_app_id', '', 'payment'],
            ['cashfree_secret_key', '', 'payment'],
            ['cashfree_env', 'sandbox', 'payment'],
            ['cashfree_api_version', '2023-08-01', 'payment'],
            ['mail_mailer', 'log', 'mail'],
            ['mail_host', '', 'mail'],
            ['mail_port', '587', 'mail'],
            ['mail_username', '', 'mail'],
            ['mail_password', '', 'mail'],
            ['mail_encryption', 'tls', 'mail'],
            ['mail_from_address', 'noreply@maku.local', 'mail'],
            ['mail_from_name', 'MAKU Union', 'mail'],
            ['meta_title', 'मिर्जापुर असंगठित कामगार यूनियन (MAKU)', 'seo'],
            ['meta_description', 'असंगठित एवं निर्माण श्रमिकों के अधिकारों, सामाजिक सुरक्षा और सम्मान के लिए समर्पित श्रमिक संगठन।', 'seo'],
            ['meta_keywords', 'MAKU, मिर्जापुर, असंगठित कामगार, श्रमिक यूनियन, BMS', 'seo'],
            ['google_analytics_id', '', 'seo'],
            ['search_console_id', '', 'seo'],
            ['notice_enabled', '1', 'flags'],
            ['gallery_home_enabled', '1', 'flags'],
            ['membership_open', '1', 'flags'],
            ['translate_enabled', '1', 'flags'],
            ['translate_kill_switch', '0', 'flags'],
            ['footer_note', 'असंगठित श्रमिकों के अधिकारों, सम्मान और सामाजिक सुरक्षा के लिए समर्पित संगठन।', 'general'],
            ['copyright_text', '© 2026 मिर्जापुर असंगठित कामगार यूनियन. सर्वाधिकार सुरक्षित।', 'general'],
            ['developer_credit', 'तकनीकी सहायता: MAKU Union डिजिटल टीम', 'general'],
        ];

        foreach ($settings as [$key, $value, $group]) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        }

        Setting::flush();
    }
}
