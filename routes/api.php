<?php

use App\Http\Controllers\Api\ApiAgencyController;
use App\Http\Controllers\Api\ApiAiController;
use App\Http\Controllers\Api\ApiCampaignController;
use App\Http\Controllers\Api\ApiClientController;
use App\Http\Controllers\Api\ApiDashboardController;
use App\Http\Controllers\Api\ApiInvoiceController;
use App\Http\Controllers\Api\ApiSocialAccountController;
use App\Http\Controllers\Api\ApiSocialPostController;
use App\Http\Controllers\Api\ApiWorkflowController;
use App\Http\Controllers\WorkflowWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth')->as('api.')->group(function () {
    Route::get('/status', fn () => ['status' => 'ok']);

    // Dashboard
    Route::get('/dashboard', [ApiDashboardController::class, 'index']);

    // Resources with dedicated controllers
    Route::apiResource('posts', ApiSocialPostController::class);
    Route::apiResource('accounts', ApiSocialAccountController::class);
    Route::apiResource('campaigns', ApiCampaignController::class);
    Route::apiResource('clients', ApiClientController::class);
    Route::apiResource('invoices', ApiInvoiceController::class);
    Route::apiResource('workflows', ApiWorkflowController::class);

    // AI
    Route::post('/ai/generate', [ApiAiController::class, 'generate']);

    // Agency
    Route::get('/agency/settings', [ApiAgencyController::class, 'settings']);
    Route::put('/agency/settings', [ApiAgencyController::class, 'updateSettings']);
    Route::get('/agency/team', [ApiAgencyController::class, 'team']);
    Route::get('/agency/billing', [ApiAgencyController::class, 'billing']);
});

// Public webhook endpoint (no auth)
Route::post('workflows/{workflow}/webhook/{secret}', [WorkflowWebhookController::class, 'handle'])->name('api.workflows.webhook');
