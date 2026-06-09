<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\EntryLogController as AdminEntryLogController;
use App\Http\Controllers\Admin\ParkingSlotController;
use App\Http\Controllers\Admin\PaymentReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Driver\BookingController;
use App\Http\Controllers\Driver\DriverDashboardController;
use App\Http\Controllers\Driver\PaymentController;
use App\Http\Controllers\Driver\ProfileController;
use App\Http\Controllers\Driver\VehicleController;
use App\Http\Controllers\Guard\EntryLogController as GuardEntryLogController;
use App\Http\Controllers\Guard\GuardDashboardController;
use App\Http\Controllers\Guard\LprCameraController;
use App\Http\Controllers\Guard\SlotMonitorController;
use App\Http\Controllers\Guard\VerificationController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('password.email');
    Route::get('/reset-password', [ForgotPasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::middleware('role:driver')->prefix('driver')->name('driver.')->group(function () {
        Route::get('/dashboard', DriverDashboardController::class)->name('dashboard');
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::get('/vehicles', [VehicleController::class, 'index'])->name('vehicles.index');
        Route::post('/vehicles', [VehicleController::class, 'store'])->name('vehicles.store');
        Route::delete('/vehicles/{vehicle}', [VehicleController::class, 'destroy'])->name('vehicles.destroy');
        Route::get('/bookings/search', [BookingController::class, 'search'])->name('bookings.search');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/bookings/{booking}/pay', [PaymentController::class, 'checkout'])->name('payments.checkout');
        Route::post('/bookings/{booking}/pay', [PaymentController::class, 'pay'])->name('payments.pay');
        Route::get('/payments/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payments.invoice');
    });

    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', AdminDashboardController::class)->name('dashboard');
        Route::get('/zones', [ZoneController::class, 'index'])->name('zones.index');
        Route::post('/zones', [ZoneController::class, 'store'])->name('zones.store');
        Route::put('/zones/{zone}', [ZoneController::class, 'update'])->name('zones.update');
        Route::delete('/zones/{zone}', [ZoneController::class, 'destroy'])->name('zones.destroy');
        Route::get('/slots', [ParkingSlotController::class, 'index'])->name('slots.index');
        Route::post('/slots', [ParkingSlotController::class, 'store'])->name('slots.store');
        Route::put('/slots/{slot}', [ParkingSlotController::class, 'update'])->name('slots.update');
        Route::delete('/slots/{slot}', [ParkingSlotController::class, 'destroy'])->name('slots.destroy');
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::get('/bookings', [AdminBookingController::class, 'index'])->name('bookings.index');
        Route::post('/bookings/{booking}/cancel', [AdminBookingController::class, 'cancel'])->name('bookings.cancel');
        Route::get('/payments', [PaymentReportController::class, 'index'])->name('payments.index');
        Route::get('/payments/export', [PaymentReportController::class, 'export'])->name('payments.export');
        Route::get('/settings', [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
        Route::get('/logs', [AdminEntryLogController::class, 'index'])->name('logs.index');
    });

    Route::middleware('role:guard')->prefix('guard')->name('guard.')->group(function () {
        Route::get('/dashboard', GuardDashboardController::class)->name('dashboard');
        Route::get('/verify', [VerificationController::class, 'index'])->name('verify.index');
        Route::post('/verify/qr', [VerificationController::class, 'qr'])->name('verify.qr');
        Route::post('/verify/plate', [VerificationController::class, 'plate'])->name('verify.plate');
        Route::get('/slots', [SlotMonitorController::class, 'index'])->name('slots.index');
        Route::get('/logs', [GuardEntryLogController::class, 'index'])->name('logs.index');
        Route::get('/lpr', [LprCameraController::class, 'show'])->name('lpr.camera');
        Route::post('/lpr/recognize', [LprCameraController::class, 'recognize'])->name('lpr.recognize');
    });
});
