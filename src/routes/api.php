<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\TaxStatusController;
use App\Http\Controllers\Api\AgreementController;
use App\Http\Controllers\Api\TaxIdValidationController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\BrokerController;
use App\Http\Controllers\Api\AuditLogController;


Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('me', [AuthController::class, 'me']);

    Route::prefix('tax-statuses')->group(function () {
        Route::get('/', [TaxStatusController::class, 'index']);
        Route::post('/', [TaxStatusController::class, 'store']);
        Route::get('/{id}', [TaxStatusController::class, 'show']);
        Route::put('/{id}', [TaxStatusController::class, 'update']);
        Route::delete('/{id}', [TaxStatusController::class, 'destroy']);
    });

    Route::prefix('agreements')->group(function () {
        Route::get('/', [AgreementController::class, 'index']);
        Route::post('/', [AgreementController::class, 'store']);
        Route::get('/{id}', [AgreementController::class, 'show']);
        Route::put('/{id}', [AgreementController::class, 'update']);
        Route::delete('/{id}', [AgreementController::class, 'destroy']);
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{id}', [CategoryController::class, 'show']);
        Route::put('/{id}', [CategoryController::class, 'update']);
        Route::delete('/{id}', [CategoryController::class, 'destroy']);
    });

    Route::prefix('brokers')->group(function () {
        Route::get('/', [BrokerController::class, 'index']);
        Route::post('/', [BrokerController::class, 'store']);
        Route::get('/{id}', [BrokerController::class, 'show']);
        Route::put('/{id}', [BrokerController::class, 'update']);
        Route::delete('/{id}', [BrokerController::class, 'destroy']);
    });

    Route::prefix('providers')->group(function () {
        Route::get('/', [ProviderController::class, 'index']);
        Route::post('/', [ProviderController::class, 'store']);
        Route::get('/{id}', [ProviderController::class, 'show']);
        Route::put('/{id}', [ProviderController::class, 'update']);
        Route::delete('/{id}', [ProviderController::class, 'destroy']);
    });

    Route::post('tax-id/validate', [TaxIdValidationController::class, 'validate']);

    // Audit Log routes (Logs de Auditoría)
    Route::prefix('audit-logs')->group(function () {
        Route::get('/', [AuditLogController::class, 'index']);
        Route::get('/{id}', [AuditLogController::class, 'show']);
    });
});

