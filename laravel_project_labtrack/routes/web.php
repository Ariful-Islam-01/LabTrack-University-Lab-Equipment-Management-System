<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\FineController;

// Public Routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware(['auth.session'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/equipment', [EquipmentController::class, 'index'])
        ->name('equipment.index');

    // Equipment Management (Only LAB_ASSISTANT)
    Route::middleware(['role:LAB_ASSISTANT'])->group(function () {
        Route::get('/equipment/create', [EquipmentController::class, 'create'])
            ->name('equipment.create');

        Route::post('/equipment', [EquipmentController::class, 'store'])
            ->name('equipment.store');

        Route::get('/equipment/{equipment}/edit', [EquipmentController::class, 'edit'])
            ->name('equipment.edit');

        Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])
            ->name('equipment.update');

        Route::delete('/equipment/{equipment}', [EquipmentController::class, 'destroy'])
            ->name('equipment.destroy');

        Route::post('/borrows/issue/{booking}', [BorrowController::class, 'issue'])
            ->name('borrows.issue');

        Route::post('/borrows/return/{borrow}', [BorrowController::class, 'returnEquipment'])
            ->name('borrows.return');
    });

    // Bookings (Index accessible by multiple roles)
    Route::get('/bookings', [BookingController::class, 'index'])
        ->name('bookings.index');

    // Student Booking Routes
    Route::middleware(['role:STUDENT'])->group(function () {
        Route::get('/bookings/create/{equipment}', [BookingController::class, 'create'])
            ->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])
            ->name('bookings.store');
    });

    // Teacher Booking Routes
    Route::middleware(['role:TEACHER'])->group(function () {
        Route::post('/bookings/{booking}/approve', [BookingController::class, 'approve'])
            ->name('bookings.approve');
        Route::post('/bookings/{booking}/reject', [BookingController::class, 'reject'])
            ->name('bookings.reject');
    });

    // Borrows (Accessible by STUDENT & LAB_ASSISTANT)
    Route::get('/borrows', [BorrowController::class, 'index'])
        ->name('borrows.index');

    // Fines (Accessible by LAB_ASSISTANT and STUDENT)
    Route::get('/fines', [FineController::class, 'index'])
        ->name('fines.index')
        ->middleware('role:LAB_ASSISTANT,STUDENT');

    Route::post('/fines/generate/{borrow}', [FineController::class, 'generate'])
        ->name('fines.generate')
        ->middleware('role:LAB_ASSISTANT');

    Route::post('/fines/pay/{fine}', [FineController::class, 'markPaid'])
        ->name('fines.markPaid')
        ->middleware('role:LAB_ASSISTANT');

    // Reports (Index accessible by multiple roles)
    Route::get('/reports', function () {
        abort(501);
    })->name('reports.index');
});
