<?php

namespace App\Services;

use App\Models\Translation;
use App\Models\TranslationUsage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class TranslationService
{
    public function enabled(): bool
    {
        if (! config('maku.translate.enabled') || ! config('maku.translate.api_key')) {
            return false;
        }

        return setting('translate_enabled', '1') !== '0'
            && setting('translate_kill_switch', '0') !== '1';
    }

    public function translate(string $text, string $source = 'en'): string
    {
        $text = trim($text);

        if ($text === '' || $this->isHindi($text)) {
            return $text;
        }

        $key = 'auto.'.sha1($text);

        $stored = Translation::query()
            ->where('locale', 'hi')
            ->where('key', $key)
            ->value('value');

        if ($stored) {
            return $stored;
        }

        if (! $this->enabled() || $source === 'hi') {
            return $text;
        }

        return Cache::remember('maku.translation.'.$key, config('maku.translate.cache_ttl'), function () use ($text, $key) {
            return $this->requestTranslation($text, $key) ?? $text;
        });
    }

    public function remainingQuota(): int
    {
        $limit = (int) config('maku.translate.monthly_limit', 500);
        $used = (int) TranslationUsage::query()
            ->where('provider', 'google')
            ->where('month', now()->format('Y-m'))
            ->value('characters');

        return max(0, $limit - $used);
    }

    public function status(): array
    {
        return [
            'enabled' => $this->enabled(),
            'configured' => (bool) config('maku.translate.api_key'),
            'monthly_limit' => (int) config('maku.translate.monthly_limit', 500),
            'remaining' => $this->remainingQuota(),
            'pending_review' => Translation::query()->where('reviewed', false)->count(),
        ];
    }

    private function requestTranslation(string $text, string $key): ?string
    {
        if ($this->remainingQuota() < strlen($text)) {
            Log::warning('Translation quota reached; skipping machine translation.');
            $this->recordFailure($text);

            return null;
        }

        [$protected, $tokens] = $this->protectTokens($text);

        try {
            $response = Http::timeout(15)->post('https://translation.googleapis.com/language/translate/v2', [
                'key' => config('maku.translate.api_key'),
                'q' => $protected,
                'source' => 'en',
                'target' => 'hi',
                'format' => 'text',
            ]);

            if ($response->failed()) {
                Log::warning('Translation provider failed', ['status' => $response->status()]);
                $this->recordFailure($text);

                return null;
            }

            $translated = data_get($response->json(), 'data.translations.0.translatedText');

            if (! is_string($translated) || $translated === '') {
                return null;
            }

            $translated = $this->restoreTokens($translated, $tokens);

            Translation::updateOrCreate(
                ['locale' => 'hi', 'key' => $key],
                ['value' => $translated, 'source' => 'google', 'reviewed' => false]
            );

            $this->recordUsage(strlen($text));

            return $translated;
        } catch (\Throwable $e) {
            Log::warning('Translation request error', ['error' => $e->getMessage()]);
            $this->recordFailure($text);

            return null;
        }
    }

    private function protectTokens(string $text): array
    {
        $tokens = [];
        $patterns = [
            '~https?://\S+~u',
            '~[\w.+-]+@[\w-]+\.[\w.]+~u',
            '~\{\{[^}]+\}\}~u',
            '~:[a-zA-Z_]+~u',
        ];

        $protected = preg_replace_callback($patterns, function (array $match) use (&$tokens) {
            $placeholder = '__MAKU_TOKEN_'.count($tokens).'__';
            $tokens[$placeholder] = $match[0];

            return $placeholder;
        }, $text) ?? $text;

        return [$protected, $tokens];
    }

    private function restoreTokens(string $text, array $tokens): string
    {
        return str_replace(array_keys($tokens), array_values($tokens), $text);
    }

    private function isHindi(string $text): bool
    {
        return (bool) preg_match('/\p{Devanagari}/u', $text);
    }

    private function recordUsage(int $characters): void
    {
        $usage = TranslationUsage::firstOrCreate(
            ['provider' => 'google', 'month' => now()->format('Y-m')],
            ['characters' => 0, 'calls' => 0]
        );

        $usage->increment('characters', $characters);
        $usage->increment('calls');
    }

    private function recordFailure(string $text): void
    {
        Translation::updateOrCreate(
            ['locale' => 'hi', 'key' => 'failed.'.sha1($text)],
            ['value' => Str::limit($text, 1000), 'source' => 'fallback', 'reviewed' => false]
        );
    }
}
