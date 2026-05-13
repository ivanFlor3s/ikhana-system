<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'message' => 'Ikhana API',
        'version' => '1.0.0',
        'documentation' => '/api/documentation'
    ]);
});

