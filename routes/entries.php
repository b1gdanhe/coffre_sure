<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\VaultController;


Route::get('user/identifiants', [EntryController::class, 'index'])->middleware(['auth', 'verified', 'role:user'])->name('entries.index');

