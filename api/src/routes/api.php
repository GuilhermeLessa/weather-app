<?php

use App\Http\Controllers\ForecastController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])
    ->get('/forecast/list', [ForecastController::class, 'list']);

Route::middleware(['auth:sanctum'])
    ->get('/forecast', [ForecastController::class, 'find']);

Route::middleware(['auth:sanctum'])
    ->delete('/forecast/{uuid}', [ForecastController::class, 'inactivate']);
