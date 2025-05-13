<?php
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\NewPasswordController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

Route::prefix('admin')->middleware(['auth'])->group(function () {
    Route::get('/attendances', [AdminController::class, 'index'])->name('admin.attendances');
    
    // Attendance routes
    Route::get('/attendance/{id}', [AttendanceController::class, 'show'])->name('admin.attendance.show');
    Route::delete('/attendances/{attendance}', [AttendanceController::class, 'destroy'])
        ->name('admin.attendance.destroy');
    
    // Employee routes
    Route::post('/employees', [AdminController::class, 'storeEmployee'])->name('admin.employees.store');
    Route::delete('/employees/{employee}', [AdminController::class, 'destroyEmployee'])
        ->name('admin.employees.destroy');
    Route::put('/employees/{employee}', [AdminController::class, 'updateEmployee'])
        ->name('admin.employees.update');

    // Search Functionality Routes
    Route::get('/employees/search', [AdminController::class, 'searchEmployees'])
        ->name('admin.employees.search');
        
    Route::get('/attendances/search', [AdminController::class, 'searchAttendances'])
        ->name('admin.attendances.search');

    // Export route
    Route::get('/export', [AdminController::class, 'export'])->name('admin.export');
});

Route::get('/attendance', [AttendanceController::class, 'create'])->name('attendance.create');
Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
Route::get('/attendance/check/{employee}', [AttendanceController::class, 'checkAttendance'])->name('attendance.check');

// Only include login and password reset routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::prefix('account')->middleware(['auth'])->group(function () {
    Route::get('/settings', [AccountController::class, 'showSettingsForm'])->name('account.settings');
    Route::post('/update-name', [AccountController::class, 'updateName'])->name('account.update-name');
    Route::post('/update-email', [AccountController::class, 'updateEmail'])->name('account.update-email');
    Route::post('/update-password', [AccountController::class, 'updatePassword'])->name('account.update-password');
});

Route::prefix('notifications')->middleware(['auth'])->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/{notification}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});