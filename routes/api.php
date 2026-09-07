<?php

use App\Http\Controllers\Api\ApiCampaignController;
use App\Http\Controllers\Api\ApiClientController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\ApiInvoiceController;
use App\Http\Controllers\Api\ApiSocialAccountController;
use App\Http\Controllers\Api\ApiSocialPostController;
use App\Http\Controllers\WorkflowWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware('auth')->as('api.')->group(function () {
    Route::get('/status', fn () => ['status' => 'ok']);

    // Dashboard
    Route::get('/dashboard', [ApiController::class, 'dashboard']);

    // Resources with dedicated controllers
    Route::apiResource('posts', ApiSocialPostController::class);
    Route::apiResource('accounts', ApiSocialAccountController::class);
    Route::apiResource('campaigns', ApiCampaignController::class);
    Route::apiResource('clients', ApiClientController::class);
    Route::apiResource('invoices', ApiInvoiceController::class);

    // Workflow & Content (keep using ApiController for now — needs future decomposition)
    Route::apiResource('workflows', ApiController::class);
    Route::apiResource('content', ApiController::class);

    // AI
    Route::post('/ai/generate', [ApiController::class, 'aiGenerate']);

    // Agency
    Route::get('/agency/settings', [ApiController::class, 'agencySettings']);
    Route::put('/agency/settings', [ApiController::class, 'updateAgencySettings']);
    Route::get('/agency/team', [ApiController::class, 'team']);
    Route::get('/agency/billing', [ApiController::class, 'billing']);
});

// Public webhook endpoint (no auth)
Route::post('workflows/{workflow}/webhook/{secret}', [WorkflowWebhookController::class, 'handle'])->name('api.workflows.webhook');
