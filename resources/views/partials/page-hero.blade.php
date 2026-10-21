<section class="page-hero">
    <div class="container">
        <h1>{{ $title }}</h1>
        @if (! empty($subtitle))
            <p>{{ $subtitle }}</p>
        @endif
        <nav aria-label="ब्रेडक्रम्ब">
            <ol class="breadcrumb-maku">
                <li><a href="{{ route('home') }}">मुख्य पृष्ठ</a></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page">{{ $title }}</li>
            </ol>
        </nav>    </div>
</section>
