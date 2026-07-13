<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BorrowController;
use App\Http\Controllers\FineController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;

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

    // Reports Dashboard (Index accessible by LAB_ASSISTANT, TEACHER, STUDENT)
    Route::get('/reports', [ReportController::class, 'index'])
        ->name('reports.index')
        ->middleware('role:LAB_ASSISTANT,TEACHER,STUDENT');

    // LAB_ASSISTANT Reports
    Route::get('/reports/equipment', [ReportController::class, 'equipmentReport'])
        ->name('reports.equipment')
        ->middleware('role:LAB_ASSISTANT');

    Route::get('/reports/borrows', [ReportController::class, 'borrowReport'])
        ->name('reports.borrows')
        ->middleware('role:LAB_ASSISTANT');

    Route::get('/reports/fines', [ReportController::class, 'fineReport'])
        ->name('reports.fines')
        ->middleware('role:LAB_ASSISTANT');

    // Advanced Reports (LAB_ASSISTANT only)
    Route::get('/reports/most-borrowed', [ReportController::class, 'mostBorrowed'])
        ->name('reports.most_borrowed')
        ->middleware('role:LAB_ASSISTANT');

    Route::get('/reports/top-borrowers', [ReportController::class, 'topBorrowers'])
        ->name('reports.top_borrowers')
        ->middleware('role:LAB_ASSISTANT');

    Route::get('/reports/category', [ReportController::class, 'categoryReport'])
        ->name('reports.category')
        ->middleware('role:LAB_ASSISTANT');

    Route::get('/reports/recent-activities', [ReportController::class, 'recentActivities'])
        ->name('reports.recent_activities')
        ->middleware('role:LAB_ASSISTANT');

    // TEACHER & LAB_ASSISTANT Reports
    Route::get('/reports/bookings', [ReportController::class, 'bookingReport'])
        ->name('reports.bookings')
        ->middleware('role:LAB_ASSISTANT,TEACHER');

    // STUDENT Reports
    Route::get('/reports/my-borrows', [ReportController::class, 'myBorrows'])
        ->name('reports.my_borrows')
        ->middleware('role:STUDENT');

    Route::get('/reports/my-fines', [ReportController::class, 'myFines'])
        ->name('reports.my_fines')
        ->middleware('role:STUDENT');

    // Student Management (Only LAB_ASSISTANT)
    Route::middleware(['role:LAB_ASSISTANT'])->group(function () {
        Route::get('/students', [StudentController::class, 'index'])
            ->name('students.index');
        Route::get('/students/create', [StudentController::class, 'create'])
            ->name('students.create');
        Route::post('/students', [StudentController::class, 'store'])
            ->name('students.store');
        Route::get('/students/{student}/edit', [StudentController::class, 'edit'])
            ->name('students.edit');
        Route::put('/students/{student}', [StudentController::class, 'update'])
            ->name('students.update');
        Route::delete('/students/{student}', [StudentController::class, 'destroy'])
            ->name('students.destroy');
    });

    // Teacher Management (Only LAB_ASSISTANT)
    Route::middleware(['role:LAB_ASSISTANT'])->group(function () {
        Route::get('/teachers', [TeacherController::class, 'index'])
            ->name('teachers.index');
        Route::get('/teachers/create', [TeacherController::class, 'create'])
            ->name('teachers.create');
        Route::post('/teachers', [TeacherController::class, 'store'])
            ->name('teachers.store');
        Route::get('/teachers/{teacher}/edit', [TeacherController::class, 'edit'])
            ->name('teachers.edit');
        Route::put('/teachers/{teacher}', [TeacherController::class, 'update'])
            ->name('teachers.update');
        Route::delete('/teachers/{teacher}', [TeacherController::class, 'destroy'])
            ->name('teachers.destroy');
    });
});
