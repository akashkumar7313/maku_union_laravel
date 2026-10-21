<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\GrievanceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentWebhookController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\WorkerCategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public website
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');
Route::get('/search', SearchController::class)->name('search');

Route::get('/page/{page}', [PageController::class, 'show'])->name('pages.show');
Route::get('/gallery', [PageController::class, 'gallery'])->name('gallery');

Route::redirect('/about-us', '/page/about-us');
Route::redirect('/our-works', '/page/our-works');
Route::redirect('/worker-info', '/page/worker-info');
Route::redirect('/organization', '/page/organization');
Route::redirect('/privacy-policy', '/page/privacy-policy');
Route::redirect('/terms', '/page/terms');
Route::redirect('/refund-policy', '/page/refund-policy');

Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/news/{news}', [NewsController::class, 'show'])->name('news.show');

Route::get('/worker-categories', [WorkerCategoryController::class, 'index'])->name('categories.index');
Route::get('/worker-categories/{workerCategory}', [WorkerCategoryController::class, 'show'])->name('categories.show');

Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:public-forms')
    ->name('contact.store');

/*
|--------------------------------------------------------------------------
| Membership
|--------------------------------------------------------------------------
*/

Route::prefix('membership')->name('membership.')->group(function () {
    Route::get('/register', [MembershipController::class, 'create'])->name('register');
    Route::post('/', [MembershipController::class, 'store'])
        ->middleware('throttle:public-forms')
        ->name('store');

    Route::get('/lookup', [MembershipController::class, 'lookupForm'])->name('lookup');
    Route::post('/lookup', [MembershipController::class, 'lookup'])
        ->middleware('throttle:public-forms')
        ->name('lookup.submit');

    Route::get('/forgot-membership', [MembershipController::class, 'forgotForm'])->name('forgot');
    Route::post('/forgot-membership', [MembershipController::class, 'forgot'])
        ->middleware('throttle:public-forms')
        ->name('forgot.submit');

    Route::get('/payment/{payment:order_no}', [MembershipController::class, 'payment'])
        ->middleware('signed')->name('payment');
    Route::post('/payment/{payment:order_no}/sandbox', [MembershipController::class, 'simulateSandbox'])
        ->middleware(['signed', 'throttle:public-forms'])->name('payment.sandbox');
    Route::post('/payment/{payment:order_no}/utr', [MembershipController::class, 'submitUtr'])
        ->middleware(['signed', 'throttle:public-forms'])->name('payment.utr');
    Route::post('/payment/{payment:order_no}/cash', [MembershipController::class, 'cash'])
        ->middleware(['signed', 'throttle:public-forms'])->name('payment.cash');
    Route::post('/payment/{payment:order_no}/receipt', [MembershipController::class, 'uploadReceipt'])
        ->middleware(['signed', 'throttle:public-forms'])->name('payment.receipt');
    Route::get('/payment/{payment:order_no}/cashfree/return', [MembershipController::class, 'cashfreeReturn'])
        ->name('payment.cashfree.return');

    Route::get('/receipt/{payment:order_no}', [MembershipController::class, 'receipt'])
        ->middleware('signed')->name('receipt');

    Route::get('/{member}/card', [MembershipController::class, 'card'])
        ->middleware('signed')->name('card');
    Route::get('/{member}/renew', [MembershipController::class, 'renewForm'])
        ->middleware('signed')->name('renew.form');
    Route::post('/{member}/renew', [MembershipController::class, 'renew'])
        ->middleware(['signed', 'throttle:public-forms'])->name('renew.store');
});

/*
|--------------------------------------------------------------------------
| Grievances
|--------------------------------------------------------------------------
*/

Route::prefix('grievance')->name('grievances.')->group(function () {
    Route::get('/', [GrievanceController::class, 'create'])->name('create');
    Route::post('/', [GrievanceController::class, 'store'])
        ->middleware('throttle:public-forms')->name('store');

    Route::get('/track', [GrievanceController::class, 'trackForm'])->name('track');
    Route::post('/track', [GrievanceController::class, 'track'])
        ->middleware('throttle:public-forms')->name('track.submit');

    Route::get('/{grievance:case_no}', [GrievanceController::class, 'show'])
        ->middleware('signed')->name('show');
});

/*
|--------------------------------------------------------------------------
| Payment webhook
|--------------------------------------------------------------------------
*/

Route::post('/webhooks/cashfree', [PaymentWebhookController::class, 'cashfree'])
    ->name('webhooks.cashfree');

/*
|--------------------------------------------------------------------------
| Administration
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [Admin\AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [Admin\AuthController::class, 'login'])
            ->middleware('throttle:login')->name('login.attempt');
    });

    Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');

    Route::middleware('admin')->group(function () {
        Route::get('/', Admin\DashboardController::class)->name('dashboard');

        Route::get('/profile', [Admin\ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [Admin\ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [Admin\ProfileController::class, 'password'])->name('profile.password');

        Route::resource('/members', Admin\MemberController::class);
        Route::post('/members/{member}/status', [Admin\MemberController::class, 'updateStatus'])->name('members.status');
        Route::post('/members/{member}/payment', [Admin\MemberController::class, 'recordPayment'])->name('members.payment');
        Route::get('/members-export', [Admin\MemberController::class, 'export'])->name('members.export');

        Route::get('/payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments-export', [Admin\PaymentController::class, 'export'])->name('payments.export');
        Route::post('/payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');
        Route::post('/payments/{payment}/refund', [Admin\PaymentController::class, 'refund'])->name('payments.refund');

        Route::get('/grievances', [Admin\GrievanceController::class, 'index'])->name('grievances.index');
        Route::get('/grievances-export', [Admin\GrievanceController::class, 'export'])->name('grievances.export');
        Route::get('/grievances/{grievance:case_no}', [Admin\GrievanceController::class, 'show'])->name('grievances.show');
        Route::post('/grievances/{grievance:case_no}/update', [Admin\GrievanceController::class, 'update'])->name('grievances.update');
        Route::post('/grievances/{grievance:case_no}/documents', [Admin\GrievanceController::class, 'uploadDocument'])->name('grievances.documents.store');
        Route::get('/grievances/{grievance:case_no}/documents/{document}', [Admin\GrievanceController::class, 'downloadDocument'])->name('grievances.documents.download');
        Route::delete('/grievances/{grievance:case_no}/documents/{document}', [Admin\GrievanceController::class, 'deleteDocument'])->name('grievances.documents.destroy');

        Route::resource('/news', Admin\NewsController::class)->except('show');
        Route::resource('/news-categories', Admin\NewsCategoryController::class)->except(['show', 'create', 'edit']);
        Route::resource('/worker-categories', Admin\WorkerCategoryController::class)->except('show');
        Route::resource('/gallery', Admin\GalleryController::class)->except('show');
        Route::resource('/notices', Admin\NoticeController::class)->except('show');
        Route::resource('/menus', Admin\MenuController::class)->except('show');
        Route::resource('/banners', Admin\BannerController::class)->except(['show', 'create', 'edit']);
        Route::resource('/action-cards', Admin\ActionCardController::class)->except(['show', 'create', 'edit']);
        Route::resource('/mission-cards', Admin\MissionCardController::class)->except(['show', 'create', 'edit']);
        Route::resource('/team-members', Admin\TeamMemberController::class)->except('show');
        Route::resource('/branches', Admin\BranchController::class)->except('show');

        Route::get('/pages', [Admin\PageController::class, 'index'])->name('pages.index');
        Route::get('/pages/{page}/edit', [Admin\PageController::class, 'edit'])->name('pages.edit');
        Route::put('/pages/{page}', [Admin\PageController::class, 'update'])->name('pages.update');

        Route::get('/settings', [Admin\SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [Admin\SettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/test-mail', [Admin\SettingController::class, 'testMail'])->name('settings.test-mail');

        Route::get('/contact-messages', [Admin\ContactMessageController::class, 'index'])->name('contact-messages.index');
        Route::get('/contact-messages/{message}', [Admin\ContactMessageController::class, 'show'])->name('contact-messages.show');
        Route::delete('/contact-messages/{message}', [Admin\ContactMessageController::class, 'destroy'])->name('contact-messages.destroy');

        Route::get('/reports', [Admin\ReportController::class, 'index'])->name('reports.index');
        Route::get('/reports/export', [Admin\ReportController::class, 'export'])->name('reports.export');

        Route::get('/translations', [Admin\TranslationController::class, 'index'])->name('translations.index');
        Route::put('/translations/{translation}', [Admin\TranslationController::class, 'update'])->name('translations.update');
        Route::post('/translations/{translation}/review', [Admin\TranslationController::class, 'review'])->name('translations.review');

        Route::middleware('admin:admin')->group(function () {
            Route::resource('/users', Admin\UserController::class)->except('show');
            Route::get('/audit-logs', [Admin\AuditLogController::class, 'index'])->name('audit-logs.index');
            Route::get('/audit-logs-export', [Admin\AuditLogController::class, 'export'])->name('audit-logs.export');
        });
    });
});
