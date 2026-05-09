<?php

use App\Http\Controllers\ColocController;
use App\Http\Controllers\QrCodeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\SetupController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ColocController::class, 'index'])->name('landing');
Route::post('/', [ColocController::class, 'store'])->name('coloc.store');

Route::prefix('{coloc:share_code}')->group(function () {
    Route::get('/', [ColocController::class, 'show'])->name('coloc.dashboard');
    Route::post('/complete', [ColocController::class, 'complete'])->name('coloc.complete');

    Route::get('/setup', [SetupController::class, 'index'])->name('coloc.setup');
    Route::post('/setup/roommate', [SetupController::class, 'storeRoommate'])->name('coloc.setup.roommate.store');
    Route::delete('/setup/roommate/{roommate}', [SetupController::class, 'destroyRoommate'])->name('coloc.setup.roommate.destroy')->scopeBindings();
    Route::post('/setup/tasks', [SetupController::class, 'storeTasks'])->name('coloc.setup.tasks.store');

    Route::get('/settings', [SettingsController::class, 'index'])->name('coloc.settings');
    Route::post('/settings/roommate', [SettingsController::class, 'storeRoommate'])->name('coloc.settings.roommate.store');
    Route::delete('/settings/roommate/{roommate}', [SettingsController::class, 'destroyRoommate'])->name('coloc.settings.roommate.destroy')->scopeBindings();
    Route::post('/settings/tasks', [SettingsController::class, 'storeTasks'])->name('coloc.settings.tasks.store');
    Route::post('/settings/task/create', [SettingsController::class, 'storeCustomTask'])->name('coloc.settings.task.create');
    Route::delete('/settings/task/{task}', [SettingsController::class, 'destroyTask'])->name('coloc.settings.task.destroy')->scopeBindings();

    Route::get('/qr', [QrCodeController::class, 'index'])->name('coloc.qr');
    Route::get('/qr/coloc.svg', [QrCodeController::class, 'coloc'])->name('coloc.qr.coloc');
    Route::get('/qr/{task}.svg', [QrCodeController::class, 'task'])->name('coloc.qr.task');
    Route::get('/task/{task}', [QrCodeController::class, 'showTask'])->name('coloc.task');
});
