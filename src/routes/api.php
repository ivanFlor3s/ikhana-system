<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\TaxStatusController;
use App\Http\Controllers\Api\AgreementController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Tax Status routes (Posiciones frente al IVA)
Route::prefix('tax-statuses')->group(function () {
    Route::get('/', [TaxStatusController::class, 'index']);
    Route::post('/', [TaxStatusController::class, 'store']);
    Route::get('/{id}', [TaxStatusController::class, 'show']);
    Route::put('/{id}', [TaxStatusController::class, 'update']);
    Route::delete('/{id}', [TaxStatusController::class, 'destroy']);
});

// Agreement routes (Convenios)
Route::prefix('agreements')->group(function () {
    Route::get('/', [AgreementController::class, 'index']);
    Route::post('/', [AgreementController::class, 'store']);
    Route::get('/{id}', [AgreementController::class, 'show']);
    Route::put('/{id}', [AgreementController::class, 'update']);
    Route::delete('/{id}', [AgreementController::class, 'destroy']);
});

// Provider routes (Proveedores)
Route::prefix('providers')->group(function () {
    Route::get('/', [ProviderController::class, 'index']);
    Route::post('/', [ProviderController::class, 'store']);
    Route::get('/{id}', [ProviderController::class, 'show']);
    Route::put('/{id}', [ProviderController::class, 'update']);
    Route::delete('/{id}', [ProviderController::class, 'destroy']);
});

