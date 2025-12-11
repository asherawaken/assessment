<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\JournalEntryController;
use App\Http\Controllers\Api\TrialBalanceController;

Route::apiResource('accounts', AccountController::class);
Route::apiResource('journal-entries', JournalEntryController::class);
Route::get('trial-balance', [TrialBalanceController::class, 'index']);

