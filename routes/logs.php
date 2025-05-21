<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\ActivityLogController;

Route::get('admin/journals', [ActivityLogController::class, 'index'])->middleware(['auth', 'verified','role:admin'])->name('logs.index');
