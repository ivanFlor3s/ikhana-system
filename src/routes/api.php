<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProviderController;
use App\Http\Controllers\Api\EnumController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Enum routes - System options for selects
Route::get('/tax-statuses', [EnumController::class, 'getTaxStatuses']);
Route::get('/agreements', [EnumController::class, 'getAgreements']);

// Provider routes
Route::prefix('providers')->group(function () {
    Route::get('/', [ProviderController::class, 'index']);
    Route::post('/', [ProviderController::class, 'store']);
    Route::get('/{id}', [ProviderController::class, 'show']);
    Route::put('/{id}', [ProviderController::class, 'update']);
    Route::delete('/{id}', [ProviderController::class, 'destroy']);
});

