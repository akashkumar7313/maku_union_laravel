<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Models\Notice;
use App\Models\Page;
use App\Models\Setting;
use App\Models\WorkerCategory;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Carbon::setLocale(config('app.locale'));
        Paginator::useBootstrapFive();

        $this->hydrateSettings();

        RateLimiter::for('login', function (Request $request) {
            return Limit::perMinute(5)->by(Str::lower((string) $request->input('email')).'|'.$request->ip());
        });

        RateLimiter::for('public-forms', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        View::composer(['layouts.public', 'home', 'partials.page-hero'], function ($view) {
            $menu = MenuItem::query()
                ->active()
                ->whereNull('parent_id')
                ->with(['children' => fn ($query) => $query->where('is_active', true)])
                ->orderBy('sort_order')
                ->get();

            $view->with([
                'menuItems' => $menu,
                'liveNotices' => Notice::query()->live()->latest()->limit(5)->get(),
                'footerPages' => Page::query()->active()->where('show_in_footer', true)->orderBy('title')->get(),
                'navCategories' => WorkerCategory::query()->active()->ordered()->get(),
            ]);
        });
    }

    private function hydrateSettings(): void
    {
        try {
            $settings = Setting::allCached();
        } catch (\Throwable) {
            return;
        }

        if ($settings->isEmpty()) {
            return;
        }

        $configMap = [
            'maku.payment.gateway' => 'payment_gateway',
            'maku.payment.cashfree.app_id' => 'cashfree_app_id',
            'maku.payment.cashfree.secret_key' => 'cashfree_secret_key',
            'maku.payment.cashfree.env' => 'cashfree_env',
            'maku.payment.cashfree.api_version' => 'cashfree_api_version',
        ];

        foreach ($configMap as $configKey => $settingKey) {
            $value = $settings->get($settingKey);

            if (filled($value)) {
                config([$configKey => $value]);
            }
        }

        $mailer = $settings->get('mail_mailer');

        if (filled($mailer)) {
            config(['mail.default' => $mailer]);
        }

        $mailConfigMap = [
            'mail.mailers.smtp.host' => 'mail_host',
            'mail.mailers.smtp.port' => 'mail_port',
            'mail.mailers.smtp.username' => 'mail_username',
            'mail.mailers.smtp.password' => 'mail_password',
            'mail.from.address' => 'mail_from_address',
            'mail.from.name' => 'mail_from_name',
        ];

        foreach ($mailConfigMap as $configKey => $settingKey) {
            $value = $settings->get($settingKey);

            if (filled($value)) {
                config([$configKey => $configKey === 'mail.mailers.smtp.port' ? (int) $value : $value]);
            }
        }

        $encryption = (string) $settings->get('mail_encryption');

        if ($encryption !== '') {
            config(['mail.mailers.smtp.scheme' => $encryption === 'ssl' ? 'smtps' : null]);
        }
    }
}
