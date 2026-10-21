@if ($errors->any())
    <div class="alert alert-danger" role="alert">
        <p class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>कृपया निम्न त्रुटियाँ ठीक करें:</p>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
