<?php

use App\Http\Controllers\VaultController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;


Route::get('coffre-fort', [VaultController::class, 'index'])->middleware(['auth', 'verified'])->name('volts.index');

Route::post('/coffre-fort/switch', [VaultController::class, 'switchVault'])->name('vaults.switch');
Route::post('/vaults', [VaultController::class, 'store'])->name('vaults.store');
