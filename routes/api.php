<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SsoWebhookController;
use App\Http\Controllers\Api\LogController;
use App\Http\Middleware\VerifyApiKey;

// Endpoint untuk menerima perintah logout dari Keycloak
Route::post('/sso/logout', [SsoWebhookController::class, 'backchannelLogout']);
Route::post('/log-activity', [LogController::class, 'store'])->middleware(VerifyApiKey::class);