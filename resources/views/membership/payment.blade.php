@extends('layouts.public')

@section('title', 'सदस्यता भुगतान | '.setting('site_name_hi', config('app.name')))

@section('content')
    @include('partials.page-hero', ['title' => 'सदस्यता भुगतान', 'subtitle' => 'भुगतान पूरा करके सदस्यता सक्रिय करें'])

    <section class="section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-7">
                    <div class="card-form">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-3">
                            <div>
                                <h2 class="h4 mb-1">भुगतान विवरण</h2>
                                <p class="small text-muted mb-0">ऑर्डर संख्या: <span class="fw-semibold">{{ $payment->order_no }}</span></p>
                            </div>
                            <span class="badge text-bg-{{ $payment->statusColor() }}">{{ $payment->statusLabel() }}</span>
                        </div>

                        @include('partials.errors')

                        <div class="payment-summary mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">सदस्य का नाम</span>
                                <strong>{{ $member->name }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">सदस्यता संख्या</span>
                                <strong>{{ $member->membership_no }}</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">भुगतान प्रकार</span>
                                <strong>{{ $payment->type === 'renewal' ? 'नवीनीकरण' : 'नई सदस्यता' }}</strong>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">सदस्यता शुल्क</span>
                                <span>{{ inr($payment->amount) }}</span>
                            </div>
                            @if ($feeAmount > 0 || $taxAmount > 0)
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">ऑनलाइन सुविधा शुल्क ({{ rtrim(rtrim(number_format($feePercent, 2), '0'), '.') }}%)</span>
                                    <span>{{ inr($feeAmount) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted">GST ({{ rtrim(rtrim(number_format($gstPercent, 2), '0'), '.') }}%)</span>
                                    <span>{{ inr($taxAmount) }}</span>
                                </div>
                            @endif
                            <div class="d-flex justify-content-between fs-5 mt-2">
                                <span class="fw-semibold">कुल देय राशि</span>
                                <span class="fw-bold text-success">{{ inr($totalAmount) }}</span>
                            </div>
                            <div class="form-hint mt-1">
                                Cashfree ऑनलाइन भुगतान पर उपरोक्त कुल राशि देय है। UPI/QR या बैंक खाते से सीधे भुगतान और नकद भुगतान पर केवल {{ inr($payment->amount) }} देय है।
                            </div>
                        </div>

                        <h3 class="h6 mb-3">भुगतान का तरीका चुनें</h3>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <h4 class="h6 mb-2"><i class="bi bi-globe2 me-1 text-primary"></i> ऑनलाइन भुगतान</h4>
                                    <p class="small text-muted">Cashfree से कार्ड/नेटबैंकिंग/UPI भुगतान पर {{ inr($totalAmount) }} (शुल्क सहित) देय है। UPI/QR या बैंक खाते से सीधे भुगतान पर केवल {{ inr($payment->amount) }} देय है।</p>

                                    @if ($cashRequested)
                                        <div class="alert alert-warning small mb-0">
                                            <i class="bi bi-hourglass-split me-1"></i> आपने नकद भुगतान विकल्प चुना है; प्रशासक की स्वीकृति की प्रतीक्षा है।
                                        </div>
                                    @elseif ($receiptUploaded)
                                        <div class="alert alert-success small mb-0">
                                            <i class="bi bi-check-circle-fill me-1"></i> आपकी भुगतान रसीद अपलोड हो चुकी है। प्रशासक सत्यापन के बाद सदस्यता सक्रिय होगी।
                                        </div>
                                    @else
                                        @if ($gateway === 'cashfree' && $cashfreeSessionId)
                                            <button class="btn btn-orange w-100" id="cashfreePay" type="button">
                                                <i class="bi bi-shield-lock me-1"></i> {{ inr($totalAmount) }} का सुरक्षित भुगतान करें
                                            </button>
                                            <p class="form-hint text-center mt-2 mb-3">Cashfree Payments द्वारा सुरक्षित भुगतान।</p>
                                        @elseif ($gateway === 'sandbox' && ! app()->environment('production'))
                                            <div class="alert alert-info small">
                                                <i class="bi bi-info-circle-fill me-1"></i> यह परीक्षण (सैंडबॉक्स) भुगतान है। कोई वास्तविक राशि नहीं कटेगी।
                                            </div>
                                            <form action="{{ $sandboxAction }}" method="POST" data-submit-once>
                                                @csrf
                                                <button class="btn btn-orange w-100" type="submit" data-loading-text="भुगतान हो रहा है...">
                                                    <i class="bi bi-shield-check me-1"></i> परीक्षण भुगतान पूरा करें
                                                </button>
                                            </form>
                                        @else
                                            <div class="alert alert-warning small">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i> सीधा ऑनलाइन गेटवे अभी उपलब्ध नहीं है। आप UPI/QR या बैंक खाते से भुगतान कर रसीद अपलोड कर सकते हैं।
                                            </div>
                                        @endif

                                        <hr class="my-3">
                                        <div class="small fw-semibold mb-2">या UPI/QR / बैंक खाते से सीधे {{ inr($payment->amount) }} का भुगतान किया?</div>
                                        <form action="{{ $receiptAction }}" method="POST" enctype="multipart/form-data" data-submit-once>
                                            @csrf
                                            <div class="d-flex flex-wrap gap-3 mb-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" id="mediumUpi" name="medium" type="radio" value="upi" @checked(old('medium') === 'upi')>
                                                    <label class="form-check-label" for="mediumUpi">UPI / QR</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" id="mediumBank" name="medium" type="radio" value="bank" @checked(old('medium') === 'bank')>
                                                    <label class="form-check-label" for="mediumBank">बैंक खाता</label>
                                                </div>
                                            </div>

                                            <div class="d-none" id="receiptUploadFields">
                                                <div class="d-none border rounded p-2 mb-2 small" id="mediumUpiDetails">
                                                    <div class="fw-semibold mb-1">UPI / QR विवरण</div>
                                                    @if (filled(setting('payment_qr')))
                                                        <img class="img-fluid rounded border mb-2" src="{{ setting_image_url('payment_qr') }}" alt="UPI QR" style="max-width: 150px;">
                                                    @endif
                                                    @if (filled(setting('upi_id')))
                                                        <div>UPI ID: <strong>{{ setting('upi_id') }}</strong></div>
                                                    @endif
                                                </div>
                                                <div class="d-none border rounded p-2 mb-2 small" id="mediumBankDetails">
                                                    <div class="fw-semibold mb-1">बैंक खाता विवरण</div>
                                                    @if (filled(setting('account_name')))
                                                        <div>खाता धारक: <strong>{{ setting('account_name') }}</strong></div>
                                                    @endif
                                                    @if (filled(setting('bank_name')))
                                                        <div>बैंक: <strong>{{ setting('bank_name') }}</strong></div>
                                                    @endif
                                                    @if (filled(setting('account_number')))
                                                        <div>खाता संख्या: <strong>{{ setting('account_number') }}</strong></div>
                                                    @endif
                                                    @if (filled(setting('ifsc')))
                                                        <div>IFSC: <strong>{{ setting('ifsc') }}</strong></div>
                                                    @endif
                                                </div>

                                                <label class="form-label" for="receipt">भुगतान रसीद अपलोड करें <span class="required">*</span></label>
                                                <input class="form-control mb-2" id="receipt" name="receipt" type="file" accept="image/jpeg,image/png,image/webp,application/pdf" required>
                                                <div class="form-hint mb-2">JPG, PNG, WEBP या PDF · अधिकतम 4 MB · सीधे भुगतान की राशि {{ inr($payment->amount) }} ही है</div>
                                                <label class="form-label" for="note">UTR / संदर्भ संख्या (वैकल्पिक)</label>
                                                <input class="form-control mb-2" id="note" name="note" maxlength="500" value="{{ old('note') }}">
                                                <button class="btn btn-outline-primary w-100" type="submit" data-loading-text="अपलोड हो रहा है...">
                                                    <i class="bi bi-upload me-1"></i> रसीद अपलोड करें
                                                </button>
                                                <p class="form-hint text-center mt-2 mb-0">रसीद जाँच के बाद सदस्यता सक्रिय होगी।</p>
                                            </div>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <h4 class="h6 mb-2"><i class="bi bi-cash-coin me-1 text-success"></i> नकद भुगतान</h4>
                                    <p class="small text-muted">कार्यालय में केवल <strong>{{ inr($payment->amount) }}</strong> नकद जमा करें और ऑर्डर संख्या <strong>{{ $payment->order_no }}</strong> बताएँ। भुगतान दर्ज करने पर स्थिति <strong>लंबित</strong> रहेगी; प्रशासक की स्वीकृति के बाद सदस्यता सक्रिय होगी।</p>

                                    @if ($receiptUploaded)
                                        <div class="alert alert-info small mb-0">
                                            <i class="bi bi-info-circle-fill me-1"></i> आपने ऑनलाइन भुगतान की रसीद अपलोड कर दी है।
                                        </div>
                                    @elseif ($cashRequested)
                                        <div class="alert alert-success small mb-0">
                                            <i class="bi bi-check-circle-fill me-1"></i> नकद भुगतान अनुरोध दर्ज है। स्वीकृति के बाद सदस्यता सक्रिय हो जाएगी।
                                        </div>
                                    @else
                                        <form action="{{ $cashAction }}" method="POST" data-submit-once>
                                            @csrf
                                            <button class="btn btn-outline-success w-100" type="submit" data-loading-text="दर्ज हो रहा है..." data-confirm="क्या आपने कार्यालय में नकद भुगतान किया है? यह अनुरोध प्रशासक की स्वीकृति तक लंबित रहेगा।">
                                                <i class="bi bi-cash-stack me-1"></i> नकद भुगतान दर्ज करें
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <p class="small text-muted mb-0">
                            भुगतान में समस्या होने पर कार्यालय से संपर्क करें और ऑर्डर संख्या <strong>{{ $payment->order_no }}</strong> बताएँ।
                        </p>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card-soft p-4">
                        <h2 class="h6 mb-3">महत्वपूर्ण सूचना</h2>
                        <ul class="small text-muted ps-3 mb-0">
                            <li class="mb-2">भुगतान के बाद सदस्यता {{ setting('membership_duration_text', 'एक वर्ष') }} के लिए सक्रिय होगी।</li>
                            <li class="mb-2">भुगतान की रसीद सफल भुगतान के बाद उपलब्ध होगी।</li>
                            <li class="mb-2">यह पेज सुरक्षित हस्ताक्षरित लिंक है; इसे साझा न करें।</li>
                            <li>दोहरे भुगतान की स्थिति में कार्यालय से संपर्क करें।</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    @if ($gateway === 'cashfree' && $cashfreeSessionId)
        <script src="https://sdk.cashfree.com/js/v3/cashfree.js"></script>
        <script>
            (function () {
                var button = document.getElementById('cashfreePay');
                if (!button || typeof Cashfree === 'undefined') return;

                var cashfree = Cashfree({ mode: @json($cashfreeEnv === 'production' ? 'production' : 'sandbox') });

                button.addEventListener('click', function () {
                    button.disabled = true;
                    cashfree.checkout({
                        paymentSessionId: @json($cashfreeSessionId),
                        redirectTarget: '_self'
                    }).catch(function () {
                        button.disabled = false;
                        alert('भुगतान विंडो खोलने में समस्या हुई। कृपया पुनः प्रयास करें।');
                    });
                });
            })();
        </script>
    @endif

    <script>
        (function () {
            var fields = document.getElementById('receiptUploadFields');
            var upiDetails = document.getElementById('mediumUpiDetails');
            var bankDetails = document.getElementById('mediumBankDetails');
            var radios = document.querySelectorAll('input[name="medium"]');

            if (!fields || !radios.length) return;

            function sync() {
                var selected = document.querySelector('input[name="medium"]:checked');

                fields.classList.toggle('d-none', !selected);
                upiDetails.classList.toggle('d-none', selected?.value !== 'upi');
                bankDetails.classList.toggle('d-none', selected?.value !== 'bank');
            }

            radios.forEach(function (radio) {
                radio.addEventListener('change', sync);
            });

            sync();
        })();
    </script>
@endpush
