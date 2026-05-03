<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AprioriController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// AUTH ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('transactions')->name('transactions.')->middleware('role:admin')->group(function () {
        Route::get('/', [TransactionController::class, 'index'])->name('index');
        Route::post('/import', [TransactionController::class, 'import'])->name('import');
        Route::delete('/truncate', [TransactionController::class, 'truncate'])->name('truncate');
    });

    Route::prefix('apriori')->name('apriori.')->middleware('role:admin')->group(function () {
        Route::get('/', [AprioriController::class, 'index'])->name('index');
        Route::post('/calculate', [AprioriController::class, 'calculate'])->name('calculate');
        Route::post('/store', [AprioriController::class, 'store'])->name('store');
    });

    Route::prefix('history')->name('history.')->middleware('role:admin,owner')->group(function () {
        Route::get('/', [HistoryController::class, 'index'])->name('index');
        Route::get('/{id}', [HistoryController::class, 'show'])->name('show');
        Route::delete('/{id}', [HistoryController::class, 'destroy'])->name('destroy');
    });

    Route::resource('users', UserController::class)->middleware('role:owner')->except(['show', 'create', 'edit']);
});
