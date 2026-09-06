<?php

use App\Http\Controllers\DocsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Documentation Routes
|--------------------------------------------------------------------------
|
| These routes serve the API documentation and OpenAPI specification.
| They are publicly accessible so developers can review the API
| without needing an API token.
|
*/

// Swagger UI - Interactive API documentation
Route::get('/api/docs', [DocsController::class, 'swaggerUi'])->name('api.docs');

// OpenAPI specification - YAML format
Route::get('/api/docs/openapi.yaml', [DocsController::class, 'openapiYaml'])->name('api.openapi.yaml');

// OpenAPI specification - JSON format
Route::get('/api/docs/openapi.json', [DocsController::class, 'openapiJson'])->name('api.openapi.json');
