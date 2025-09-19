<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

Route::prefix('todos')->group(function () {
    Route::post('/', [TodoController::class, 'store']);
    Route::get('/export', [TodoController::class, 'export']);
});

Route::get('/chart', [ChartController::class, 'index']);
