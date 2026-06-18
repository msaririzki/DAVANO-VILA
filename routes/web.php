<?php

use App\Http\Controllers\AddonItemController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\WebSettingController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\BookingAddonController;
use App\Http\Controllers\BookingAddonPaymentController;
use App\Http\Controllers\BookingAdjustmentController;
use App\Http\Controllers\BookingCancellationController;
use App\Http\Controllers\BookingDetailController;
use App\Http\Controllers\BookingInvoiceController;
use App\Http\Controllers\BookingPaymentController;
use App\Http\Controllers\BookingReceiptShareController;
use App\Http\Controllers\BookingRefundController;
use App\Http\Controllers\BookingStatusController;
use App\Http\Controllers\BookingTransferIssueController;
use App\Http\Controllers\BookingUnitAssignmentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InternalBookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicMediaSettingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomStatusController;
use App\Http\Controllers\RoomUnitStatusController;
use App\Http\Controllers\VillaContactSettingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicBookingController::class, 'index'])->name('public.home');
Route::get('/rooms', [PublicBookingController::class, 'index'])->name('public.rooms.index');
Route::post('/bookings', [PublicBookingController::class, 'store'])->name('public.bookings.store');
Route::get('/bookings/{booking:public_token}', [PublicBookingController::class, 'show'])
    ->middleware('signed:lang')
    ->name('public.bookings.show');
Route::get('/receipts/{booking:public_token}/pdf', [BookingInvoiceController::class, 'public'])
    ->middleware('signed')
    ->name('public.bookings.receipt');

Route::get('/dashboard', DashboardController::class)->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/internal/bookings/create', [InternalBookingController::class, 'create'])->name('bookings.create');
    Route::post('/internal/bookings', [InternalBookingController::class, 'store'])->name('bookings.store');
    Route::get('/internal/bookings/{booking}', [BookingDetailController::class, 'show'])->name('bookings.show');
    Route::get('/internal/bookings/{booking}/invoice', [BookingInvoiceController::class, 'download'])
        ->middleware('role:admin,super_admin')
        ->name('bookings.invoice');
    Route::post('/internal/bookings/{booking}/send-receipt', BookingReceiptShareController::class)
        ->middleware('role:admin,super_admin')
        ->name('bookings.receipt.send');
    Route::post('/internal/bookings/{booking}/addons', [BookingAddonController::class, 'store'])
        ->middleware('role:admin,super_admin')
        ->name('bookings.addons.store');
    Route::patch('/booking-addons/{bookingAddon}/cancel', [BookingAddonController::class, 'cancel'])
        ->middleware('role:admin,super_admin')
        ->name('booking-addons.cancel');
    Route::patch('/internal/bookings/{booking}/addons/cancel-all', [BookingAddonController::class, 'cancelAll'])
        ->middleware('role:admin,super_admin')
        ->name('bookings.addons.cancel-all');
    Route::patch('/bookings/{booking}/status', [BookingStatusController::class, 'update'])->name('bookings.status.update');
    Route::post('/bookings/{booking}/payments', [BookingPaymentController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('bookings.payments.store');
    Route::post('/bookings/{booking}/cancel', [BookingCancellationController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('bookings.cancel');
    Route::post('/bookings/{booking}/refunds', [BookingRefundController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('bookings.refunds.store');
    Route::patch('/bookings/{booking}/transfer-issues/{payment}', [BookingTransferIssueController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('bookings.transfer-issues.update');
    Route::patch('/bookings/{booking}/adjustments', [BookingAdjustmentController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('bookings.adjustments.update');
    Route::patch('/bookings/{booking}/units', [BookingUnitAssignmentController::class, 'update'])->name('bookings.units.update');
    Route::post('/booking-addons/{bookingAddon}/payments', [BookingAddonPaymentController::class, 'store'])
        ->middleware('role:super_admin')
        ->name('booking-addons.payments.store');
    Route::patch('/rooms/{room}/status', [RoomStatusController::class, 'update'])->name('rooms.status.update');
    Route::patch('/room-units/{roomUnit}/status', [RoomUnitStatusController::class, 'update'])
        ->name('room-units.status.update');

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
    Route::patch('/settings/villa-contact', [VillaContactSettingController::class, 'update'])
        ->middleware('role:super_admin')
        ->name('settings.villa-contact.update');
    Route::get('/admin/web-settings', WebSettingController::class)
        ->middleware('role:super_admin')
        ->name('admin.web-settings');
    Route::get('/admin/reports', ReportController::class)
        ->middleware('role:super_admin')
        ->name('admin.reports');
    Route::get('/admin/audit-logs', AuditLogController::class)
        ->middleware('role:super_admin')
        ->name('admin.audit-logs');
    Route::post('/bank-accounts', [BankAccountController::class, 'store'])
        ->middleware(['role:super_admin', 'password.confirm'])
        ->name('bank-accounts.store');
    Route::patch('/bank-accounts/{bankAccount}', [BankAccountController::class, 'update'])
        ->middleware(['role:super_admin', 'password.confirm'])
        ->name('bank-accounts.update');

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
