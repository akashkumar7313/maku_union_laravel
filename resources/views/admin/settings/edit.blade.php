@extends('layouts.admin')

@section('title', 'सेटिंग्स')
@section('heading', 'वेबसाइट सेटिंग्स')
@section('subheading', 'संगठन, संपर्क, भुगतान एवं SEO')

@section('content')
    <div class="page-heading">
        <div><h1>सेटिंग्स</h1><p>यहाँ से किया गया परिवर्तन पूरी वेबसाइट पर लागू होता है।</p></div>
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('home') }}" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right me-1"></i> वेबसाइट देखें</a>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" data-submit-once>
        @csrf @method('PUT')

        <ul class="nav nav-pills flex-wrap gap-1 mb-4" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-identity" type="button" role="tab">पहचान</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-contact" type="button" role="tab">संपर्क</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-social" type="button" role="tab">सोशल मीडिया</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-about" type="button" role="tab">परिचय</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-membership" type="button" role="tab">सदस्यता</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-payment" type="button" role="tab">भुगतान</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-mail" type="button" role="tab">मेल</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-grievance" type="button" role="tab">शिकायत</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-seo" type="button" role="tab">SEO</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">ब्रांडिंग</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-flags" type="button" role="tab">सुविधाएँ</button></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-identity" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">संगठन की पहचान</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label" for="site_name_hi">संगठन का पूरा नाम (हिन्दी)</label>
                                <input class="form-control" id="site_name_hi" name="site_name_hi" value="{{ old('site_name_hi', $settings->get('site_name_hi')) }}" maxlength="200">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="site_short_name">संक्षिप्त नाम</label>
                                <input class="form-control" id="site_short_name" name="site_short_name" value="{{ old('site_short_name', $settings->get('site_short_name')) }}" maxlength="50">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="site_name_en">संगठन का नाम (अंग्रेज़ी)</label>
                                <input class="form-control" id="site_name_en" name="site_name_en" value="{{ old('site_name_en', $settings->get('site_name_en')) }}" maxlength="200">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="founded_year">स्थापना वर्ष</label>
                                <input class="form-control" id="founded_year" name="founded_year" value="{{ old('founded_year', $settings->get('founded_year')) }}" inputmode="numeric" maxlength="4">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="tagline_hi">टैगलाइन</label>
                                <input class="form-control" id="tagline_hi" name="tagline_hi" value="{{ old('tagline_hi', $settings->get('tagline_hi')) }}" maxlength="300">
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="affiliation_hi">संबद्धता</label>
                                <input class="form-control" id="affiliation_hi" name="affiliation_hi" value="{{ old('affiliation_hi', $settings->get('affiliation_hi')) }}" maxlength="200">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="registration_no">पंजीकरण संख्या</label>
                                <input class="form-control" id="registration_no" name="registration_no" value="{{ old('registration_no', $settings->get('registration_no')) }}" maxlength="100">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="site_description">संगठन का संक्षिप्त विवरण</label>
                                <textarea class="form-control" id="site_description" name="site_description" rows="2" maxlength="500">{{ old('site_description', $settings->get('site_description')) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="site_keywords">खोज शब्द (keywords)</label>
                                <input class="form-control" id="site_keywords" name="site_keywords" value="{{ old('site_keywords', $settings->get('site_keywords')) }}" maxlength="500">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="copyright_text">कॉपीराइट पंक्ति</label>
                                <input class="form-control" id="copyright_text" name="copyright_text" value="{{ old('copyright_text', $settings->get('copyright_text')) }}" maxlength="200" placeholder="खाली छोड़ने पर स्वतः बनेगी">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="developer_credit">विकासकर्ता / तकनीकी सहायता</label>
                                <input class="form-control" id="developer_credit" name="developer_credit" value="{{ old('developer_credit', $settings->get('developer_credit')) }}" maxlength="200">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-contact" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">संपर्क जानकारी</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="contact_address">पता</label>
                                <input class="form-control" id="contact_address" name="contact_address" value="{{ old('contact_address', $settings->get('contact_address')) }}" maxlength="500">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="contact_phone">मोबाइल</label>
                                <input class="form-control" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings->get('contact_phone')) }}" maxlength="30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="contact_phone_alt">वैकल्पिक फ़ोन</label>
                                <input class="form-control" id="contact_phone_alt" name="contact_phone_alt" value="{{ old('contact_phone_alt', $settings->get('contact_phone_alt')) }}" maxlength="30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="contact_email">ईमेल</label>
                                <input class="form-control" id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $settings->get('contact_email')) }}" maxlength="190">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="contact_hours">कार्यालय समय</label>
                                <input class="form-control" id="contact_hours" name="contact_hours" value="{{ old('contact_hours', $settings->get('contact_hours')) }}" maxlength="150">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="map_link">Google Maps लिंक</label>
                                <input class="form-control" id="map_link" name="map_link" value="{{ old('map_link', $settings->get('map_link')) }}" maxlength="255">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="map_embed">मैप एम्बेड कोड</label>
                                <textarea class="form-control" id="map_embed" name="map_embed" rows="2" maxlength="3000">{{ old('map_embed', $settings->get('map_embed')) }}</textarea>
                                <div class="form-text">Google Maps का iframe कोड। केवल विश्वसनीय स्रोत से लगाएँ।</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-social" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">सोशल मीडिया</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label" for="facebook_url">Facebook</label><input class="form-control" id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings->get('facebook_url')) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="form-label" for="youtube_url">YouTube</label><input class="form-control" id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $settings->get('youtube_url')) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="form-label" for="whatsapp_number">WhatsApp नंबर</label><input class="form-control" id="whatsapp_number" name="whatsapp_number" value="{{ old('whatsapp_number', $settings->get('whatsapp_number')) }}" maxlength="30"></div>
                            <div class="col-md-6"><label class="form-label" for="twitter_url">X / Twitter</label><input class="form-control" id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $settings->get('twitter_url')) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="form-label" for="instagram_url">Instagram</label><input class="form-control" id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings->get('instagram_url')) }}" maxlength="255"></div>
                            <div class="col-md-6"><label class="form-label" for="telegram_url">Telegram</label><input class="form-control" id="telegram_url" name="telegram_url" value="{{ old('telegram_url', $settings->get('telegram_url')) }}" maxlength="255"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-about" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">होमपेज परिचय</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label" for="about_short">संक्षिप्त परिचय</label>
                            <textarea class="form-control" id="about_short" name="about_short" rows="3" maxlength="2000">{{ old('about_short', $settings->get('about_short')) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="about_objective">उद्देश्य</label>
                            <textarea class="form-control" id="about_objective" name="about_objective" rows="2" maxlength="2000">{{ old('about_objective', $settings->get('about_objective')) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="about_mission">मिशन</label>
                            <textarea class="form-control" id="about_mission" name="about_mission" rows="2" maxlength="2000">{{ old('about_mission', $settings->get('about_mission')) }}</textarea>
                        </div>
                        <div class="mb-0">
                            <label class="form-label" for="about_vision">दृष्टि</label>
                            <textarea class="form-control" id="about_vision" name="about_vision" rows="2" maxlength="2000">{{ old('about_vision', $settings->get('about_vision')) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-membership" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">सदस्यता</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="membership_fee">सदस्यता शुल्क (₹)</label>
                                <input class="form-control" id="membership_fee" name="membership_fee" type="number" min="0" max="100000" step="1" value="{{ old('membership_fee', $settings->get('membership_fee', 100)) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="renewal_fee">नवीनीकरण शुल्क (₹)</label>
                                <input class="form-control" id="renewal_fee" name="renewal_fee" type="number" min="0" max="100000" step="1" value="{{ old('renewal_fee', $settings->get('renewal_fee', 100)) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="membership_duration_text">अवधि (प्रदर्शन हेतु)</label>
                                <input class="form-control" id="membership_duration_text" name="membership_duration_text" value="{{ old('membership_duration_text', $settings->get('membership_duration_text', 'एक वर्ष')) }}" maxlength="60">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="membership_card_note">सदस्यता कार्ड पर नोट</label>
                                <textarea class="form-control" id="membership_card_note" name="membership_card_note" rows="2" maxlength="500">{{ old('membership_card_note', $settings->get('membership_card_note')) }}</textarea>
                            </div>
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" id="membership_open" name="membership_open" type="checkbox" value="1" @checked(old('membership_open', $settings->get('membership_open', '1') === '1'))>
                                    <label class="form-check-label" for="membership_open">ऑनलाइन सदस्यता पंजीकरण खुला है</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-payment" role="tabpanel">
                <div class="card-admin mb-4">
                    <div class="card-header">भुगतान गेटवे एवं कुंजियाँ</div>
                    <div class="card-body">
                        <div class="alert alert-light border small">
                            यहाँ किया गया बदलाव तुरंत लागू होता है। कुंजियाँ बदलने के लिए नई कुंजी भरें; खाली छोड़ने पर पुरानी कुंजी सुरक्षित रहती है।
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="payment_gateway">सक्रिय गेटवे</label>
                                <select class="form-select" id="payment_gateway" name="payment_gateway">
                                    @foreach (['sandbox' => 'डेमो / ऑफ़लाइन', 'manual' => 'मैन्युअल (UPI / बैंक)', 'cashfree' => 'Cashfree'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('payment_gateway', $settings->get('payment_gateway', config('maku.payment.gateway', 'sandbox'))) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="cashfree_env">Cashfree वातावरण</label>
                                <select class="form-select" id="cashfree_env" name="cashfree_env">
                                    <option value="sandbox" @selected(old('cashfree_env', $settings->get('cashfree_env', config('maku.payment.cashfree.env', 'sandbox'))) === 'sandbox')>Sandbox (परीक्षण)</option>
                                    <option value="production" @selected(old('cashfree_env', $settings->get('cashfree_env', config('maku.payment.cashfree.env', 'sandbox'))) === 'production')>Production (लाइव)</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="cashfree_api_version">Cashfree API संस्करण</label>
                                <input class="form-control" id="cashfree_api_version" name="cashfree_api_version" value="{{ old('cashfree_api_version', $settings->get('cashfree_api_version', config('maku.payment.cashfree.api_version', '2023-08-01'))) }}" maxlength="30">
                            </div>

                            <div class="col-12"><hr class="my-1"><h6 class="mb-0">ऑनलाइन भुगतान अतिरिक्त शुल्क</h6></div>
                            <div class="col-md-4">
                                <label class="form-label" for="gateway_fee_percent">गेटवे शुल्क (%)</label>
                                <input class="form-control" id="gateway_fee_percent" name="gateway_fee_percent" type="number" min="0" max="10" step="0.01" value="{{ old('gateway_fee_percent', $settings->get('gateway_fee_percent', config('maku.payment.fee_percent', 2))) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="gateway_gst_percent">GST (%)</label>
                                <input class="form-control" id="gateway_gst_percent" name="gateway_gst_percent" type="number" min="0" max="28" step="0.01" value="{{ old('gateway_gst_percent', $settings->get('gateway_gst_percent', config('maku.payment.gst_percent', 18))) }}">
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <div class="form-hint mb-2">यह शुल्क केवल Cashfree ऑनलाइन भुगतान पर जुड़ता है। सदस्यता शुल्क ₹100 ही संस्था को प्राप्त होगा।</div>
                            </div>

                            <div class="col-12"><hr class="my-1"><h6 class="mb-0">Cashfree कुंजियाँ</h6></div>
                            <div class="col-md-6">
                                <label class="form-label" for="cashfree_app_id">App ID / Client ID</label>
                                <input class="form-control" id="cashfree_app_id" name="cashfree_app_id" value="{{ old('cashfree_app_id', $settings->get('cashfree_app_id', config('maku.payment.cashfree.app_id'))) }}" maxlength="120" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="cashfree_secret_key">Secret Key</label>
                                <input class="form-control" id="cashfree_secret_key" name="cashfree_secret_key" type="password" value="" placeholder="{{ filled($settings->get('cashfree_secret_key', config('maku.payment.cashfree.secret_key'))) ? '•••••••• (सहेजा गया)' : 'अभी सेट नहीं' }}" maxlength="255" autocomplete="new-password">
                            </div>
                            <div class="col-12">
                                <div class="small text-muted">Cashfree Webhook URL: <code>{{ url('/webhooks/cashfree') }}</code> — इसे Cashfree डैशबोर्ड में जोड़ें।</div>
                            </div>
                            <div class="col-12">
                                <div class="alert {{ filled($settings->get('cashfree_app_id', config('maku.payment.cashfree.app_id'))) && filled($settings->get('cashfree_secret_key', config('maku.payment.cashfree.secret_key'))) ? 'alert-success' : 'alert-warning' }} mb-0 py-2 small">
                                    Cashfree: <strong>{{ filled($settings->get('cashfree_app_id', config('maku.payment.cashfree.app_id'))) && filled($settings->get('cashfree_secret_key', config('maku.payment.cashfree.secret_key'))) ? 'कुंजियाँ सेट हैं' : 'कुंजियाँ सेट नहीं हैं' }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-admin">
                    <div class="card-header">बैंक / UPI विवरण (मैन्युअल भुगतान)</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4"><label class="form-label" for="upi_id">UPI ID</label><input class="form-control" id="upi_id" name="upi_id" value="{{ old('upi_id', $settings->get('upi_id')) }}" maxlength="100"></div>
                            <div class="col-md-4"><label class="form-label" for="bank_name">बैंक का नाम</label><input class="form-control" id="bank_name" name="bank_name" value="{{ old('bank_name', $settings->get('bank_name')) }}" maxlength="150"></div>
                            <div class="col-md-4"><label class="form-label" for="account_name">खाता धारक</label><input class="form-control" id="account_name" name="account_name" value="{{ old('account_name', $settings->get('account_name')) }}" maxlength="150"></div>
                            <div class="col-md-6"><label class="form-label" for="account_number">खाता संख्या</label><input class="form-control" id="account_number" name="account_number" value="{{ old('account_number', $settings->get('account_number')) }}" maxlength="40"></div>
                            <div class="col-md-6"><label class="form-label" for="ifsc">IFSC कोड</label><input class="form-control" id="ifsc" name="ifsc" value="{{ old('ifsc', $settings->get('ifsc')) }}" maxlength="20"></div>
                            <div class="col-12">
                                <label class="form-label" for="payment_instructions">भुगतान निर्देश</label>
                                <textarea class="form-control" id="payment_instructions" name="payment_instructions" rows="3" maxlength="1000">{{ old('payment_instructions', $settings->get('payment_instructions')) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-mail" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">मेल (SMTP) सेटिंग्स</div>
                    <div class="card-body">
                        <div class="alert alert-light border small">
                            ईमेल भेजने हेतु SMTP विवरण भरें। Gmail हेतु App Password उपयोग करें। सहेजने के बाद नीचे दिए बटन से परीक्षण मेल भेज सकते हैं।
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="mail_mailer">मेल वाहक</label>
                                <select class="form-select" id="mail_mailer" name="mail_mailer">
                                    @foreach (['smtp' => 'SMTP', 'log' => 'लॉग (परीक्षण)', 'sendmail' => 'Sendmail'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('mail_mailer', $settings->get('mail_mailer', config('mail.default', 'log'))) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="mail_host">SMTP होस्ट</label>
                                <input class="form-control" id="mail_host" name="mail_host" value="{{ old('mail_host', $settings->get('mail_host', config('mail.mailers.smtp.host'))) }}" maxlength="190" placeholder="smtp.gmail.com">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="mail_port">पोर्ट</label>
                                <input class="form-control" id="mail_port" name="mail_port" type="number" min="1" max="65535" value="{{ old('mail_port', $settings->get('mail_port', config('mail.mailers.smtp.port'))) }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_username">उपयोगकर्ता नाम / ईमेल</label>
                                <input class="form-control" id="mail_username" name="mail_username" value="{{ old('mail_username', $settings->get('mail_username', config('mail.mailers.smtp.username'))) }}" maxlength="190" autocomplete="off">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="mail_password">पासवर्ड / App Password</label>
                                <input class="form-control" id="mail_password" name="mail_password" type="password" value="" placeholder="{{ filled($settings->get('mail_password', config('mail.mailers.smtp.password'))) ? '•••••••• (सहेजा गया)' : 'अभी सेट नहीं' }}" maxlength="255" autocomplete="new-password">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="mail_encryption">एन्क्रिप्शन</label>
                                <select class="form-select" id="mail_encryption" name="mail_encryption">
                                    @foreach (['tls' => 'TLS (587)', 'ssl' => 'SSL (465)', 'none' => 'कोई नहीं'] as $value => $label)
                                        <option value="{{ $value }}" @selected(old('mail_encryption', $settings->get('mail_encryption', 'tls')) === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="mail_from_address">प्रेषक ईमेल</label>
                                <input class="form-control" id="mail_from_address" name="mail_from_address" type="email" value="{{ old('mail_from_address', $settings->get('mail_from_address', config('mail.from.address'))) }}" maxlength="190">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="mail_from_name">प्रेषक नाम</label>
                                <input class="form-control" id="mail_from_name" name="mail_from_name" value="{{ old('mail_from_name', $settings->get('mail_from_name', config('mail.from.name'))) }}" maxlength="150">
                            </div>
                            <div class="col-12">
                                <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="modal" data-bs-target="#testMailModal">
                                    <i class="bi bi-envelope-paper me-1"></i> परीक्षण मेल भेजें
                                </button>
                                <span class="small text-muted ms-2">सहेजने के बाद परीक्षण करें।</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-grievance" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">शिकायत सेवा</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label" for="grievance_helpline">शिकायत हेल्पलाइन</label>
                                <input class="form-control" id="grievance_helpline" name="grievance_helpline" value="{{ old('grievance_helpline', $settings->get('grievance_helpline')) }}" maxlength="30">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="grievance_email">शिकायत ईमेल</label>
                                <input class="form-control" id="grievance_email" name="grievance_email" type="email" value="{{ old('grievance_email', $settings->get('grievance_email')) }}" maxlength="190">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="grievance_sla_days">निस्तारण अवधि (दिन)</label>
                                <input class="form-control" id="grievance_sla_days" name="grievance_sla_days" type="number" min="0" max="365" value="{{ old('grievance_sla_days', $settings->get('grievance_sla_days', 15)) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="grievance_note">शिकायत पृष्ठ पर सूचना</label>
                                <textarea class="form-control" id="grievance_note" name="grievance_note" rows="3" maxlength="1000">{{ old('grievance_note', $settings->get('grievance_note')) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-seo" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">SEO एवं विश्लेषण</div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label" for="meta_title">डिफ़ॉल्ट मेटा शीर्षक</label>
                                <input class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $settings->get('meta_title')) }}" maxlength="200">
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="meta_description">डिफ़ॉल्ट मेटा विवरण</label>
                                <textarea class="form-control" id="meta_description" name="meta_description" rows="2" maxlength="500">{{ old('meta_description', $settings->get('meta_description')) }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label" for="meta_keywords">मेटा कीवर्ड</label>
                                <input class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $settings->get('meta_keywords')) }}" maxlength="500">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="google_analytics_id">Google Analytics मापन ID</label>
                                <input class="form-control" id="google_analytics_id" name="google_analytics_id" value="{{ old('google_analytics_id', $settings->get('google_analytics_id')) }}" maxlength="40" placeholder="G-XXXXXXXXXX">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="search_console_id">Search Console सत्यापन कोड</label>
                                <input class="form-control" id="search_console_id" name="search_console_id" value="{{ old('search_console_id', $settings->get('search_console_id')) }}" maxlength="120">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-branding" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card-admin h-100">
                            <div class="card-header">लोगो एवं फ़ेविकॉन</div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label" for="logo">मुख्य लोगो</label>
                                    <div class="mb-2 p-2 bg-light rounded text-center">
                                        <img class="img-fluid" style="max-height: 90px;" src="{{ setting_image_url('site_logo', 'assets/img/logo.svg') }}" alt="वर्तमान लोगो">
                                    </div>
                                    <input class="form-control" id="logo" name="logo" type="file" accept="image/jpeg,image/png,image/webp,image/svg+xml">
                                    <div class="form-text">यह लोगो हेडर, फुटर, एडमिन पैनल, रसीद और सदस्यता कार्ड पर उपयोग होगा। JPG, PNG, WEBP या SVG; अधिकतम 2 MB।</div>
                                    @if (filled($settings->get('site_logo')))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" id="remove_logo" name="remove_logo" type="checkbox" value="1">
                                            <label class="form-check-label small text-danger" for="remove_logo">लोगो हटाकर डिफ़ॉल्ट लोगो उपयोग करें</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-0">
                                    <label class="form-label" for="favicon">फ़ेविकॉन</label>
                                    <input class="form-control" id="favicon" name="favicon" type="file" accept="image/png,image/x-icon,image/jpeg,image/webp">
                                    <div class="form-text">PNG, ICO, JPG या WEBP; अधिकतम 512 KB।</div>
                                    @if (filled($settings->get('site_favicon')))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" id="remove_favicon" name="remove_favicon" type="checkbox" value="1">
                                            <label class="form-check-label small text-danger" for="remove_favicon">फ़ेविकॉन हटाएँ</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card-admin h-100">
                            <div class="card-header">अन्य चित्र</div>
                            <div class="card-body">
                                <div class="mb-4">
                                    <label class="form-label" for="about_image">परिचय चित्र</label>
                                    @if (filled($settings->get('about_image')))
                                        <div class="mb-2"><img class="img-fluid rounded" style="max-height: 100px;" src="{{ setting_image_url('about_image') }}" alt="परिचय चित्र"></div>
                                    @endif
                                    <input class="form-control" id="about_image" name="about_image" type="file" accept="image/jpeg,image/png,image/webp">
                                    @if (filled($settings->get('about_image')))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" id="remove_about_image" name="remove_about_image" type="checkbox" value="1">
                                            <label class="form-check-label small text-danger" for="remove_about_image">परिचय चित्र हटाएँ</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="payment_qr">UPI / भुगतान QR</label>
                                    @if (filled($settings->get('payment_qr')))
                                        <div class="mb-2"><img class="img-fluid rounded" style="max-height: 120px;" src="{{ setting_image_url('payment_qr') }}" alt="भुगतान QR"></div>
                                    @endif
                                    <input class="form-control" id="payment_qr" name="payment_qr" type="file" accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text">भुगतान पृष्ठ पर UPI QR के रूप में दिखेगा।</div>
                                    @if (filled($settings->get('payment_qr')))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" id="remove_payment_qr" name="remove_payment_qr" type="checkbox" value="1">
                                            <label class="form-check-label small text-danger" for="remove_payment_qr">QR हटाएँ</label>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-0">
                                    <label class="form-label" for="og_image">साझा करने का चित्र (OG image)</label>
                                    @if (filled($settings->get('og_image')))
                                        <div class="mb-2"><img class="img-fluid rounded" style="max-height: 100px;" src="{{ setting_image_url('og_image') }}" alt="OG image"></div>
                                    @endif
                                    <input class="form-control" id="og_image" name="og_image" type="file" accept="image/jpeg,image/png,image/webp">
                                    <div class="form-text">WhatsApp/Facebook पर लिंक साझा करने पर दिखने वाला चित्र।</div>
                                    @if (filled($settings->get('og_image')))
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" id="remove_og_image" name="remove_og_image" type="checkbox" value="1">
                                            <label class="form-check-label small text-danger" for="remove_og_image">OG चित्र हटाएँ</label>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab-pane fade" id="tab-flags" role="tabpanel">
                <div class="card-admin">
                    <div class="card-header">वेबसाइट सुविधाएँ</div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" id="notice_enabled" name="notice_enabled" type="checkbox" value="1" @checked(old('notice_enabled', $settings->get('notice_enabled', '1') === '1'))>
                            <label class="form-check-label" for="notice_enabled">सूचना पट्टी दिखाएँ</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" id="gallery_home_enabled" name="gallery_home_enabled" type="checkbox" value="1" @checked(old('gallery_home_enabled', $settings->get('gallery_home_enabled', '1') === '1'))>
                            <label class="form-check-label" for="gallery_home_enabled">होमपेज पर गैलरी अनुभाग दिखाएँ</label>
                        </div>
                        <hr>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" id="translate_enabled" name="translate_enabled" type="checkbox" value="1" @checked(old('translate_enabled', $settings->get('translate_enabled', '0') === '1'))>
                            <label class="form-check-label" for="translate_enabled">मशीन अनुवाद सुविधा सक्षम करें</label>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" id="translate_kill_switch" name="translate_kill_switch" type="checkbox" value="1" @checked(old('translate_kill_switch', $settings->get('translate_kill_switch', '0') === '1'))>
                            <label class="form-check-label" for="translate_kill_switch">आपातकालीन अनुवाद बंद (kill switch)</label>
                        </div>
                        <div class="form-text mt-2">Google Translate कुंजी <code>.env</code> में कॉन्फ़िगर करें। त्रुटि होने पर मूल हिन्दी सामग्री दिखेगी।</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex gap-2 mt-4">
            <button class="btn btn-orange" type="submit"><i class="bi bi-check2 me-1"></i> सभी सेटिंग्स सहेजें</button>
            <a class="btn btn-outline-secondary" href="{{ route('admin.dashboard') }}">रद्द करें</a>
        </div>
    </form>

    <div class="modal fade" id="testMailModal" tabindex="-1" aria-labelledby="testMailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('admin.settings.test-mail') }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="testMailModalLabel">परीक्षण मेल भेजें</h5>
                        <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="बंद करें"></button>
                    </div>
                    <div class="modal-body">
                        <label class="form-label" for="test_email">ईमेल पता</label>
                        <input class="form-control" id="test_email" name="test_email" type="email" value="{{ auth()->user()?->email }}" required maxlength="190">
                        <div class="form-text">वर्तमान मेल सेटिंग्स से एक परीक्षण संदेश भेजा जाएगा।</div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">रद्द करें</button>
                        <button class="btn btn-orange" type="submit">भेजें</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
