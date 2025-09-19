<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

Route::prefix('todos')->group(function () {
    Route::post('/', [TodoController::class, 'store']);
    Route::get('/', [TodoController::class, 'index']);
    Route::get('/{todo}', [TodoController::class, 'show']);
    Route::put('/{todo}', [TodoController::class, 'update']);
    Route::patch('/{todo}', [TodoController::class, 'update']);
    Route::get('/export', [TodoController::class, 'export']);
});

Route::get('/chart', [ChartController::class, 'index']);
