<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('admin/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

Route::get('user/dashboard', function () {
    return Inertia::render('users/Dashboard');
})->middleware(['auth', 'verified', 'role:user'])->name('user.dashboard');



require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/vaults.php';
require __DIR__ . '/entries.php';
require __DIR__ . '/roles.php';
require __DIR__ . '/users.php';
