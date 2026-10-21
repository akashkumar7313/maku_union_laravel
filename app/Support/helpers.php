<?php

use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('setting')) {
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('setting_image_url')) {
    function setting_image_url(string $key, ?string $default = null): string
    {
        $path = Setting::get($key);

        if ($path) {
            return Storage::disk(config('maku.uploads.disk'))->url($path);
        }

        return $default ? asset($default) : asset('assets/img/avatar-placeholder.svg');
    }
}

if (! function_exists('upload_url')) {
    function upload_url(?string $path, string $default = 'assets/img/avatar-placeholder.svg'): string
    {
        return $path
            ? Storage::disk(config('maku.uploads.disk'))->url($path)
            : asset($default);
    }
}

if (! function_exists('inr')) {
    function inr(float|int|string|null $amount): string
    {
        return '₹'.number_format((float) $amount, 2, '.', ',');
    }
}

if (! function_exists('hindi_date')) {
    function hindi_date(?DateTimeInterface $date, string $format = 'j F Y'): string
    {
        if (! $date) {
            return '—';
        }

        return Carbon::instance($date)->locale('hi')->translatedFormat($format);
    }
}

if (! function_exists('hindi_datetime')) {
    function hindi_datetime(?DateTimeInterface $date): string
    {
        return hindi_date($date, 'j F Y, g:i A');
    }
}

if (! function_exists('mask_phone')) {
    function mask_phone(?string $phone): string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone) ?? '';

        return strlen($digits) > 4
            ? str_repeat('•', strlen($digits) - 4).substr($digits, -4)
            : $digits;
    }
}

if (! function_exists('clean_digits')) {
    function clean_digits(?string $value): string
    {
        return preg_replace('/\D+/', '', (string) $value) ?? '';
    }
}

if (! function_exists('phone_display')) {
    function phone_display(?string $phone, bool $withCountryCode = true): string
    {
        $digits = clean_digits($phone);

        if ($digits === '') {
            return '';
        }

        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        }

        $digits = ltrim($digits, '0');

        if (strlen($digits) > 10 && str_starts_with($digits, '91')) {
            $digits = substr($digits, -10);
        }

        if (strlen($digits) === 10) {
            $digits = '91'.$digits;
        }

        return $withCountryCode ? '+'.$digits : $digits;
    }
}

if (! function_exists('phone_href')) {
    function phone_href(?string $phone): string
    {
        $display = phone_display($phone);

        return $display === '' ? '' : 'tel:'.$display;
    }
}

if (! function_exists('whatsapp_url')) {
    function whatsapp_url(?string $phone, ?string $text = null): string
    {
        $number = phone_display($phone, false);
        $url = 'https://wa.me/'.$number;

        return filled($text) ? $url.'?text='.rawurlencode((string) $text) : $url;
    }
}

if (! function_exists('excerpt_hi')) {
    function excerpt_hi(?string $text, int $limit = 140): string
    {
        return Str::limit(trim(strip_tags((string) $text)), $limit);
    }
}

if (! function_exists('safe_html')) {
    /**
     * Rich text written by trusted administrators only.
     * Stored HTML is rendered after being sanitised on save.
     */
    function safe_html(?string $html): string
    {
        return (string) $html;
    }
}

if (! function_exists('youtube_embed')) {
    function youtube_embed(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        if (preg_match('~(?:youtu\.be/|v=|embed/)([A-Za-z0-9_-]{6,})~', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/'.$m[1];
        }

        return null;
    }
}

if (! function_exists('gateway_is_sandbox')) {
    function gateway_is_sandbox(): bool
    {
        return config('maku.payment.gateway') === 'sandbox';
    }
}

if (! function_exists('active_class')) {
    function active_class(string $pattern, string $class = 'active'): string
    {
        return request()->is($pattern) ? $class : '';
    }
}
