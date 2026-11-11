<?php

use App\Http\Controllers\ClientRequestController;
use App\Http\Controllers\ManagerRequestController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController; // Не забудьте импорт
use Illuminate\Support\Facades\Route;

Route::get('/', [ServiceController::class, 'index'])->name('home');
Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

// Добавляем маршрут dashboard, чтобы тесты Breeze не падали
Route::get('/dashboard', function () {
    if (auth()->user()->isManager()) {
        return redirect()->route('manager.index');
    }
    return redirect()->route('client-requests.index');
})->middleware(['auth'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Маршруты профиля из Breeze
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Кабінет клієнта
    Route::get('/my/requests', [ClientRequestController::class, 'index'])->name('client-requests.index');
    Route::get('/my/requests/create', [ClientRequestController::class, 'create'])->name('client-requests.create');
    Route::post('/my/requests', [ClientRequestController::class, 'store'])->name('client-requests.store');
    Route::get('/my/requests/{clientRequest}', [ClientRequestController::class, 'show'])->name('client-requests.show');
    Route::post('/my/requests/{clientRequest}/cancel', [ClientRequestController::class, 'cancel'])->name('client-requests.cancel');
    Route::post('/my/requests/{clientRequest}/comment', [ClientRequestController::class, 'comment'])->name('client-requests.comment');

    // Кабінет менеджера
    Route::prefix('manager')->name('manager.')->middleware('manager')->group(function () {
        Route::get('/requests', [ManagerRequestController::class, 'index'])->name('index');
        Route::get('/requests/{clientRequest}', [ManagerRequestController::class, 'show'])->name('show');
        Route::post('/requests/{clientRequest}/assign', [ManagerRequestController::class, 'assign'])->name('assign');
        Route::patch('/requests/{clientRequest}/status', [ManagerRequestController::class, 'updateStatus'])->name('status');
        Route::post('/requests/{clientRequest}/comment', [ClientRequestController::class, 'comment'])->name('comment');
    });
});

require __DIR__.'/auth.php';