<?php
use App\Http\Controllers\Livewire\AccountsPage\AccountsPageController;
use App\Http\Controllers\Livewire\AccountsPage\AccountsController;
use App\Http\Controllers\Livewire\ArchivePage\ArchivePageController;
use App\Http\Controllers\Livewire\ArchivePage\ArchiveController;
use App\Http\Controllers\Livewire\ReportsPage\ReportsPageController;
use App\Http\Controllers\Livewire\DashboardPage\DashboardController;
use App\Http\Controllers\Livewire\DashboardPage\DashboardStatsController;
use App\Http\Controllers\Livewire\LoginHistoryPage\LoginHistoryPageController;
use App\Http\Controllers\Livewire\SettingsPage\SettingsPageController;
use App\Http\Controllers\Livewire\SettingsPage\SettingsController;
use App\Http\Controllers\Livewire\ReservedRoomsPage\ReservedRoomsPageController;
use App\Http\Controllers\Livewire\StudentReservationPage\StudentReservationController;
use App\Http\Controllers\Livewire\StudentRegistrationPage\StudentRegistrationController;
use App\Http\Controllers\Livewire\ReservedRoomsPage\ReservationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportExportController;
use App\Http\Controllers\ScannerController;


use Illuminate\Support\Facades\Route;

// Barcode Scanner Homepage (Public)
Route::get('/', [ScannerController::class, 'index'])->name('scanner.index');
Route::post('/scanner/scan', [ScannerController::class, 'scan'])->name('scanner.scan');
Route::get('/scanner/today-logins', [ScannerController::class, 'getTodaysLoginsApi'])->name('scanner.today-logins');
Route::post('/scanner/set-section', [ScannerController::class, 'setSection'])->name('scanner.set-section');

// Admin password verification for scanner section switching (Public API)
Route::post('/api/verify-admin-password', [ScannerController::class, 'verifyAdminPassword'])->name('api.verify-admin-password');

// Student Room Reservation (Public)
Route::get('/reservations', [StudentReservationController::class, 'index'])->name('student.reservations');
Route::get('/reservations/available-slots', [StudentReservationController::class, 'getAvailableSlots'])->name('student.reservations.slots');
Route::post('/reservations/create', [StudentReservationController::class, 'create'])->name('student.reservations.create');

// Student Registration (Public)
Route::get('/registration', [StudentRegistrationController::class, 'index'])->name('student.registration');
Route::post('/registration', [StudentRegistrationController::class, 'register'])->name('student.registration.submit');

// Admin Login
Route::get('/login', [AuthController::class, 'render'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');



Route::middleware('auth:admin')->group(function () {
    // Section selection routes (no section.required middleware)
    Route::get('/select-section', [AuthController::class, 'selectSection'])->name('select-section');
    Route::post('/set-section', [AuthController::class, 'setSection'])->name('set-section');

    // All other routes require section to be selected
    Route::middleware('section.required')->group(function () {
        //dashboard routes
        Route::get('/dashboard', DashboardController::class)->name('admin.dashboard.index');
        Route::get('/dashboard/{timeframe}', [DashboardController::class, 'data'])->name('admin.dashboard.data');
        // stats JSON endpoint used by Alpine polling on the dashboard
        Route::get('/admin/stats.json', [DashboardStatsController::class, 'index'])->name('admin.stats.json');
        //accounts routes
        Route::get('/accounts', AccountsPageController::class)->name('admin.accounts.index');
        Route::post('/accounts/create', [AccountsController::class, 'create'])->name('admin.accounts.create');
        Route::post('/accounts/edit', [AccountsController::class, 'edit'])->name('admin.accounts.edit');
        Route::post('/accounts/delete', [AccountsController::class, 'delete'])->name('admin.accounts.delete');
        Route::post('/accounts/import', [AccountsController::class, 'import'])->name('admin.accounts.import');
        Route::post('/accounts/update-all-expiration-dates', [AccountsController::class, 'updateAllExpirationDates'])->name('admin.accounts.update-all-expiration-dates');
        //reports routes
        Route::get('/reports', ReportsPageController::class)->name('admin.reports.index');
        Route::get('/reports/export/download', [ReportExportController::class, 'download'])->name('reports.export.download');
        Route::get('/reports/print/pdf', [ReportExportController::class, 'printPdf'])->name('reports.print.pdf');
        //login history routes
        Route::get('/login-history', LoginHistoryPageController::class)->name('admin.login-history.index');
        //archive routes
        Route::get('/archive', ArchivePageController::class)->name('admin.archive.index');
        Route::post('/archive/edit', [ArchiveController::class, 'edit'])->name('admin.archive.edit');
        Route::post('/archive/delete', [ArchiveController::class, 'delete'])->name('admin.archive.delete');
        Route::post('/archive/bulk-delete', [ArchiveController::class, 'bulkDelete'])->name('admin.archive.bulk-delete');
        //settings routes
        Route::get('/settings', SettingsPageController::class)->name('admin.settings.index');
        Route::post('/settings/expiration/update', [SettingsController::class, 'updateExpirationDate'])->name('settings.expiration.update');
        Route::post('/settings/logout/update', [SettingsController::class, 'updateAutoLogout'])->name('settings.logout.update');
        Route::post('/settings/admin/update', [SettingsController::class, 'updateAdminAccount'])->name('settings.admin.update');
        Route::post('/settings/scanner-password/update', [SettingsController::class, 'updateScannerPassword'])->name('settings.scanner-password.update');

        //room reservations routes (admin)
        Route::get('/reserved-rooms', ReservedRoomsPageController::class)->name('admin.reserved-rooms.index');
        Route::get('/reserved-rooms/calendar-data', [ReservationController::class, 'getCalendarData'])->name('admin.reserved-rooms.calendar-data');
        Route::get('/reserved-rooms/reservation/{id}', [ReservationController::class, 'getReservation'])->name('admin.reserved-rooms.get');
        Route::post('/reserved-rooms/reservation/{id}/update', [ReservationController::class, 'updateReservation'])->name('admin.reserved-rooms.update');
        Route::delete('/reserved-rooms/reservation/{id}', [ReservationController::class, 'deleteReservation'])->name('admin.reserved-rooms.delete');
        Route::post('/reserved-rooms/block-time', [ReservationController::class, 'blockTimeSlot'])->name('admin.reserved-rooms.block');
        Route::post('/reserved-rooms/unblock-time', [ReservationController::class, 'unblockTimeSlot'])->name('admin.reserved-rooms.unblock');

    });
});

Route::get('/create-admin', [AuthController::class, 'debugCreateAdmin']);
