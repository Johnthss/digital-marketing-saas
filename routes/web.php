<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AgencyController;
use App\Http\Controllers\AiContentController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\OAuthController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ContentLibraryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Email\EmailCampaignController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\GdprController;
use App\Http\Controllers\InboxController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\MediaLibraryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SocialAccountController;
use App\Http\Controllers\SocialPostController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\WhiteLabelController;
use App\Http\Controllers\WorkflowController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::get('/password/reset', [ResetPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ResetPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');

Route::get('/auth/{provider}', [OAuthController::class, 'redirect'])->name('oauth.redirect');
Route::get('/auth/{provider}/callback', [OAuthController::class, 'callback'])->name('oauth.callback');

Route::middleware(['auth', 'agency'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/search', [SearchController::class, 'index'])->name('search.index');

    Route::prefix('social')->name('social.')->group(function () {
        Route::resource('accounts', SocialAccountController::class)->except('show');
        Route::post('accounts/{account}/toggle', [SocialAccountController::class, 'toggle'])->name('accounts.toggle');
        Route::resource('posts', SocialPostController::class);
        Route::post('posts/{post}/publish', [SocialPostController::class, 'publish'])->name('posts.publish');
        Route::post('posts/{post}/retry', [SocialPostController::class, 'retry'])->name('posts.retry');
        Route::post('posts/{post}/score', [SocialPostController::class, 'score'])->name('posts.score');
    });

    Route::resource('campaigns', CampaignController::class);
    Route::post('campaigns/{campaign}/status', [CampaignController::class, 'changeStatus'])->name('campaigns.status');

    Route::resource('clients', ClientController::class);

    Route::prefix('ai')->name('ai.')->group(function () {
        Route::get('/', [AiContentController::class, 'index'])->name('index');
        Route::post('/generate', [AiContentController::class, 'generate'])->name('generate');
        Route::post('/rewrite', [AiContentController::class, 'rewrite'])->name('rewrite');
        Route::post('/hashtags', [AiContentController::class, 'hashtags'])->name('hashtags');
        Route::post('/ideas', [AiContentController::class, 'ideas'])->name('ideas');
    });

    Route::get('workflows/builder', [WorkflowController::class, 'builder'])->name('workflows.builder');
    Route::get('workflows/builder/{workflow}', [WorkflowController::class, 'builder'])->name('workflows.builder.edit');
    Route::post('workflows/builder/save', [WorkflowController::class, 'storeFromBuilder'])->name('workflows.builder.save');
    Route::post('workflows/builder/update/{workflow}', [WorkflowController::class, 'updateFromBuilder'])->name('workflows.builder.update');
    Route::post('workflows/{workflow}/execute', [WorkflowController::class, 'execute'])->name('workflows.execute');
    Route::get('workflows/templates/{template}', [WorkflowController::class, 'createFromTemplate'])->name('workflows.templates.use');
    Route::resource('workflows', WorkflowController::class);
    Route::post('workflows/{workflow}/toggle', [WorkflowController::class, 'toggleStatus'])->name('workflows.toggle');
    Route::get('workflows/{workflow}/versions', [WorkflowController::class, 'versions'])->name('workflows.versions');
    Route::post('workflows/{workflow}/versions/{version}/restore', [WorkflowController::class, 'restoreVersion'])->name('workflows.versions.restore');
    Route::get('workflows/{workflow}/webhook', [WorkflowController::class, 'webhookInfo'])->name('workflows.webhook');
    Route::post('workflows/{workflow}/webhook/regenerate', [WorkflowController::class, 'regenerateWebhook'])->name('workflows.webhook.regenerate');

    Route::resource('inbox', InboxController::class)->except('create', 'store', 'edit', 'update');
    Route::post('inbox/{message}/triage', [InboxController::class, 'triage'])->name('inbox.triage');
    Route::post('inbox/{message}/reply', [InboxController::class, 'reply'])->name('inbox.reply');

    Route::resource('content', ContentLibraryController::class);

    Route::resource('landing-pages', LandingPageController::class)->except('destroy');
    Route::delete('landing-pages/{page}', [LandingPageController::class, 'destroy'])->name('landing-pages.destroy');
    Route::post('landing-pages/{page}/toggle', [LandingPageController::class, 'togglePublish'])->name('landing-pages.toggle');

    Route::resource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/paid', [InvoiceController::class, 'markPaid'])->name('invoices.paid');

    Route::resource('activity', ActivityLogController::class)->except('create', 'store', 'edit', 'update');
    Route::resource('forms', FormController::class);

    // Media Library
    Route::resource('media', MediaLibraryController::class);
    Route::get('media/{asset}/download', [MediaLibraryController::class, 'download'])->name('media.download');
    Route::post('media/{asset}/duplicate', [MediaLibraryController::class, 'duplicate'])->name('media.duplicate');
    Route::post('media/bulk-delete', [MediaLibraryController::class, 'bulkDelete'])->name('media.bulk-delete');
    Route::post('forms/{form}/toggle', [FormController::class, 'togglePublish'])->name('forms.toggle');
    Route::resource('webhooks', WebhookController::class);

    // Reports
    Route::resource('reports', ReportController::class);
    Route::get('reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
    Route::post('reports/{report}/generate', [ReportController::class, 'generate'])->name('reports.generate');

    // Email Campaigns
    Route::prefix('email')->name('email.')->group(function () {
        Route::get('/campaigns', [EmailCampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/campaigns/create', [EmailCampaignController::class, 'create'])->name('campaigns.create');
        Route::post('/campaigns', [EmailCampaignController::class, 'store'])->name('campaigns.store');
        Route::get('/campaigns/{campaign}', [EmailCampaignController::class, 'show'])->name('campaigns.show');
        Route::get('/campaigns/{campaign}/edit', [EmailCampaignController::class, 'edit'])->name('campaigns.edit');
        Route::put('/campaigns/{campaign}', [EmailCampaignController::class, 'update'])->name('campaigns.update');
        Route::delete('/campaigns/{campaign}', [EmailCampaignController::class, 'destroy'])->name('campaigns.destroy');
        Route::post('/campaigns/{campaign}/send', [EmailCampaignController::class, 'send'])->name('campaigns.send');
        Route::post('/campaigns/{campaign}/add-clients', [EmailCampaignController::class, 'addClients'])->name('campaigns.add-clients');
    });

    // White-Label
    Route::get('white-label', [WhiteLabelController::class, 'index'])->name('white-label.index');
    Route::post('white-label', [WhiteLabelController::class, 'update'])->name('white-label.update');

    // GDPR
    Route::get('privacy', [GdprController::class, 'index'])->name('gdpr.index');
    Route::post('privacy/export', [GdprController::class, 'requestExport'])->name('gdpr.export');
    Route::post('privacy/delete', [GdprController::class, 'requestDeletion'])->name('gdpr.delete');
    Route::post('privacy/consent', [GdprController::class, 'updateConsent'])->name('gdpr.consent');

    // Billing & Subscription
    Route::get('agency/billing', [BillingController::class, 'index'])->name('agency.billing');
    Route::get('agency/billing/checkout/{plan}', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::get('agency/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('agency/billing/cancel', [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::post('agency/billing/cancel-subscription', [BillingController::class, 'cancelSubscription'])->name('billing.cancel-subscription');
    Route::get('agency/invoices', [BillingController::class, 'invoices'])->name('agency.invoices');
    Route::get('agency/invoices/{invoice}/download', [BillingController::class, 'downloadInvoice'])->name('billing.invoice.download');
});

// Billing webhook (public - Stripe can't authenticate)
Route::post('billing/webhook', [BillingController::class, 'webhook'])->name('billing.webhook');

Route::middleware(['auth', 'agency'])->group(function () {
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    Route::get('/two-factor', [TwoFactorController::class, 'show'])->name('two-factor.show');
    Route::post('/two-factor/enable', [TwoFactorController::class, 'enable'])->name('two-factor.enable');
    Route::post('/two-factor/verify', [TwoFactorController::class, 'verify'])->name('two-factor.verify');
    Route::post('/two-factor/disable', [TwoFactorController::class, 'disable'])->name('two-factor.disable');

    Route::get('/onboarding', function () {
        return view('onboarding');
    })->name('onboarding');

    Route::prefix('agency')->name('agency.')->group(function () {
        Route::get('settings', [AgencyController::class, 'settings'])->name('settings');
        Route::put('settings', [AgencyController::class, 'updateSettings'])->name('settings.update');
        Route::get('team', [AgencyController::class, 'team'])->name('team');
        Route::post('team/invite', [AgencyController::class, 'inviteMember'])->name('team.invite');
        Route::put('team/{member}/role', [AgencyController::class, 'updateMemberRole'])->name('team.role');
        Route::delete('team/{member}', [AgencyController::class, 'removeMember'])->name('team.remove');
        Route::get('billing', [AgencyController::class, 'billing'])->name('billing');
        Route::post('billing/upgrade', [AgencyController::class, 'upgrade'])->name('billing.upgrade');
    });

});

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()->toISOString()]);
});

// Version/Changelog (PUBLIC - no auth required)
require __DIR__.'/version.php';

// Telegram integration
require __DIR__.'/telegram.php';

// API Documentation (public)
require __DIR__.'/docs.php';
