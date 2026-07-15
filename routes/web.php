<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('penjualan')->name('penjualan.')->group(function () {
    Route::get('/', [PenjualanController::class, 'index'])->name('index');
    Route::get('/create', [PenjualanController::class, 'create'])->name('create');
    Route::post('/', [PenjualanController::class, 'store'])->name('store');
    Route::get('/{id}', [PenjualanController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PenjualanController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PenjualanController::class, 'update'])->name('update');
    Route::delete('/{id}', [PenjualanController::class, 'destroy'])->name('destroy');
});

Route::prefix('pembayaran')->name('pembayaran.')->group(function () {
    Route::get('/', [PembayaranController::class, 'index'])->name('index');
    Route::get('/create', [PembayaranController::class, 'create'])->name('create');
    Route::post('/', [PembayaranController::class, 'store'])->name('store');
    Route::get('/{id}', [PembayaranController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PembayaranController::class, 'edit'])->name('edit');
    Route::put('/{id}', [PembayaranController::class, 'update'])->name('update');
    Route::delete('/{id}', [PembayaranController::class, 'destroy'])->name('destroy');
});

Route::prefix('master/user')->name('user.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/', [UserController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('destroy');
});

Route::prefix('master/item')->name('item.')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('index');
    Route::get('/create', [ItemController::class, 'create'])->name('create');
    Route::post('/', [ItemController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [ItemController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ItemController::class, 'update'])->name('update');
    Route::delete('/{id}', [ItemController::class, 'destroy'])->name('destroy');
});