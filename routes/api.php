<?php

use App\Http\Controllers\TodoController;
use App\Http\Controllers\ChartController;
use Illuminate\Support\Facades\Route;

Route::get('todos/export', [TodoController::class, 'export']);
Route::get('chart', [ChartController::class, 'index']);

Route::apiResource('todos', TodoController::class);
