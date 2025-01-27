<?php

use App\Http\Controllers\BonusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/bonus/credit', [BonusController::class, 'credit']);
Route::post('/bonus/debit', [BonusController::class, 'debit']);
Route::get('/bonus/balance/{client_id}', [BonusController::class, 'checkBalance']);

