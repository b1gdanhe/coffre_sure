<?php

use App\Http\Controllers\UserController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::get('utilisateurs', [UserController::class, 'index'])->middleware(['auth', 'verified'])->name('users.index');
