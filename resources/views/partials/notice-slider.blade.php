@if (setting('notice_enabled', '1') === '1' && isset($liveNotices) && $liveNotices->isNotEmpty())
    <div class="notice-strip">
        <div class="container notice-inner">
            <span class="notice-label"><i class="bi bi-megaphone-fill"></i> सूचना</span>
            <div class="notice-viewport">
                <div class="notice-track">
                    @foreach ([0, 1] as $copy)
                        <div class="notice-group" @if ($copy) aria-hidden="true" @endif>
                            @foreach ($liveNotices as $notice)
                                <span class="notice-item" title="{{ $notice->title }}">
                                    @if ($notice->url)
                                        <a href="{{ str_starts_with($notice->url, 'http') ? $notice->url : url($notice->url) }}" @if ($copy) tabindex="-1" @endif><i class="bi bi-dot"></i> {{ $notice->title }}</a>
                                    @else
                                        <span><i class="bi bi-dot"></i> {{ $notice->title }}</span>
                                    @endif
                                </span>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
