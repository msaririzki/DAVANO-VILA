<?php

use App\Http\Controllers\AddonItemController;
use App\Http\Controllers\BookingAddonController;
use App\Http\Controllers\BookingAddonPaymentController;
use App\Http\Controllers\BookingAdjustmentController;
use App\Http\Controllers\BookingDetailController;
use App\Http\Controllers\BookingPaymentController;
use App\Http\Controllers\BookingStatusController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicMediaSettingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomStatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicBookingController::class, 'index'])->name('public.home');
Route::get('/rooms', [PublicBookingController::class, 'index'])->name('public.rooms.index');
Route::post('/bookings', [PublicBookingController::class, 'store'])->name('public.bookings.store');
Route::get('/bookings/{booking:public_token}', [PublicBookingController::class, 'show'])
    ->middleware('signed')
    ->name('public.bookings.show');

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/internal/bookings/{booking}', [BookingDetailController::class, 'show'])->name('bookings.show');
    Route::post('/internal/bookings/{booking}/addons', [BookingAddonController::class, 'store'])->name('bookings.addons.store');
    Route::patch('/bookings/{booking}/status', [BookingStatusController::class, 'update'])->name('bookings.status.update');
    Route::post('/bookings/{booking}/payments', [BookingPaymentController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('bookings.payments.store');
    Route::patch('/bookings/{booking}/adjustments', [BookingAdjustmentController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('bookings.adjustments.update');
    Route::post('/booking-addons/{bookingAddon}/payments', [BookingAddonPaymentController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('booking-addons.payments.store');
    Route::patch('/rooms/{room}/status', [RoomStatusController::class, 'update'])->name('rooms.status.update');

    Route::get('/rooms-admin', [RoomController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('rooms.index');
    Route::get('/rooms-admin/create', [RoomController::class, 'create'])
        ->middleware('role:super_admin')
        ->name('rooms.create');
    Route::post('/rooms-admin', [RoomController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('rooms.store');
    Route::get('/rooms-admin/{room}/edit', [RoomController::class, 'edit'])
        ->middleware('role:super_admin')
        ->name('rooms.edit');
    Route::patch('/rooms-admin/{room}', [RoomController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('rooms.update');
    Route::patch('/settings/public-media', [PublicMediaSettingController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('settings.public-media.update');

    Route::get('/addon-items', [AddonItemController::class, 'index'])
        ->middleware('role:super_admin')
        ->name('addon-items.index');
    Route::post('/addon-items', [AddonItemController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('addon-items.store');
    Route::patch('/addon-items/{addonItem}', [AddonItemController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('addon-items.update');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
