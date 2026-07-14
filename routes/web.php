<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Rute untuk Halaman Home
Route::get('/', function () {
    return Inertia::render('Home', [
        'judul_dari_laravel' => 'Dashboard Utama SPA'
    ]);
});

// Rute untuk Halaman About
Route::get('/about', function () {
    return Inertia::render('About');
});

Route::get('/profile', function () {
    return Inertia::render('Profile');
});