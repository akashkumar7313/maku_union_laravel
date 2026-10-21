@php
    $isEdit = $isEdit ?? false;
@endphp
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card-admin">
            <div class="card-header">व्यक्तिगत जानकारी</div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label" for="name">पूरा नाम <span class="required">*</span></label>
                        <input class="form-control" id="name" name="name" value="{{ old('name', $member?->name) }}" required maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="guardian_name">पिता / पति का नाम</label>
                        <input class="form-control" id="guardian_name" name="guardian_name" value="{{ old('guardian_name', $member?->guardian_name) }}" maxlength="150">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="phone">मोबाइल नंबर <span class="required">*</span></label>
                        <input class="form-control" id="phone" name="phone" value="{{ old('phone', $member?->phone) }}" required inputmode="numeric" maxlength="15">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="email">ईमेल</label>
                        <input class="form-control" id="email" name="email" type="email" value="{{ old('email', $member?->email) }}" maxlength="190">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="gender">लिंग</label>
                        <select class="form-select" id="gender" name="gender">
                            <option value="">चुनें</option>
                            <option value="male" @selected(old('gender', $member?->gender) === 'male')>पुरुष</option>
                            <option value="female" @selected(old('gender', $member?->gender) === 'female')>महिला</option>
                            <option value="other" @selected(old('gender', $member?->gender) === 'other')>अन्य</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="dob">जन्म तिथि</label>
                        <input class="form-control" id="dob" name="dob" type="date" value="{{ old('dob', $member?->dob?->format('Y-m-d')) }}" max="{{ now()->subDay()->format('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label" for="address">पूरा पता <span class="required">*</span></label>
                        <textarea class="form-control" id="address" name="address" rows="2" required minlength="5" maxlength="500">{{ old('address', $member?->address) }}</textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="district">जनपद <span class="required">*</span></label>
                        <input class="form-control" id="district" name="district" value="{{ old('district', $member?->district ?? 'मिर्जापुर') }}" required maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="block">ब्लॉक / तहसील</label>
                        <input class="form-control" id="block" name="block" value="{{ old('block', $member?->block) }}" maxlength="100">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label" for="aadhaar_number">आधार संख्या</label>
                        <input class="form-control" id="aadhaar_number" name="aadhaar_number" value="{{ old('aadhaar_number') }}" inputmode="numeric" maxlength="12" placeholder="{{ $member?->aadhaar_last4 ? '•••• •••• '.$member->aadhaar_last4 : '12 अंकों का आधार नंबर' }}">
                        <div class="form-hint">बदलने के लिए नया 12 अंकों का नंबर भरें; खाली छोड़ने पर पुराना सुरक्षित रहेगा।</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="worker_category_id">श्रमिक श्रेणी <span class="required">*</span></label>
                        <select class="form-select" id="worker_category_id" name="worker_category_id" required>
                            <option value="">श्रेणी चुनें</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) old('worker_category_id', $member?->worker_category_id) === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($isEdit)
                        <div class="col-md-3">
                            <label class="form-label" for="valid_from">वैधता प्रारंभ</label>
                            <input class="form-control" id="valid_from" name="valid_from" type="date" value="{{ old('valid_from', $member?->valid_from?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="valid_until">वैधता समाप्ति</label>
                            <input class="form-control" id="valid_until" name="valid_until" type="date" value="{{ old('valid_until', $member?->valid_until?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="notes">आंतरिक टिप्पणी</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" maxlength="2000">{{ old('notes', $member?->notes) }}</textarea>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card-admin">
            <div class="card-header">फ़ोटो एवं दस्तावेज़</div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <img class="avatar-sm mb-2" id="photoPreview" style="width: 96px; height: 96px;" src="{{ $member?->photoUrl() ?? asset('assets/img/avatar-placeholder.svg') }}" alt="फ़ोटो पूर्वावलोकन">
                </div>
                <div class="mb-3">
                    <label class="form-label" for="photo">फ़ोटो</label>
                    <input class="form-control" id="photo" name="photo" type="file" accept="image/jpeg,image/png,image/webp" data-image-preview="#photoPreview">
                    <div class="form-text">JPG, PNG या WEBP, अधिकतम 2 MB।</div>
                </div>
                <div class="mb-0">
                    <label class="form-label" for="id_document">पहचान दस्तावेज़</label>
                    <input class="form-control" id="id_document" name="id_document" type="file" accept="image/jpeg,image/png,application/pdf">
                    <div class="form-text">JPG, PNG या PDF, अधिकतम 4 MB।</div>
                    @if ($member?->id_document)
                        <div class="form-text mt-1"><i class="bi bi-paperclip"></i> दस्तावेज़ पहले से अपलोड है।</div>
                    @endif
                </div>
            </div>
        </div>
        @if ($isEdit)
            <div class="card-admin mt-4">
                <div class="card-header">सदस्यता स्थिति</div>
                <div class="card-body">
                    <p class="mb-2">वर्तमान स्थिति: <span class="badge text-bg-{{ $member->statusColor() }}">{{ $member->statusLabel() }}</span></p>
                    <p class="small text-muted mb-0">स्थिति परिवर्तन एवं भुगतान प्रविष्टि सदस्य विवरण पृष्ठ से करें।</p>
                </div>
            </div>
        @endif
    </div>
</div>
