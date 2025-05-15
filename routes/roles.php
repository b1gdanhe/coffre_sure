<?php

use App\Http\Controllers\RoleController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::get('roles', [RoleController::class, 'index'])->middleware(['auth', 'verified'])->name('roles.index');
