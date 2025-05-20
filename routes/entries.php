<?php

use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\VaultController;


Route::group(['middleware' => ['auth', 'verified', 'role:user']], function () {
    Route::get('user/identifiants', [EntryController::class, 'index'])->name('entries.index');
    Route::post('user/identifiants', [EntryController::class, 'store'])->name('entries.store');
});
