@if (session('success'))
    <div class="container mt-3">
        <div class="alert alert-success alert-dismissible fade show" role="alert" data-auto-dismiss>
            <i class="bi bi-check-circle-fill me-2"></i>{!! session('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="बंद करें"></button>
        </div>
    </div>
@endif
@if (session('error'))
    <div class="container mt-3">
        <div class="alert alert-danger alert-dismissible fade show" role="alert" data-auto-dismiss>
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{!! session('error') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="बंद करें"></button>
        </div>
    </div>
@endif
