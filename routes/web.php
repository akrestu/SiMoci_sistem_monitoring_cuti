<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\JenisCutiController;
use App\Http\Controllers\TransportasiController;
use App\Http\Controllers\TransportasiDetailController;
use App\Http\Controllers\CutiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MemoKompensasiController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Autentikasi routes (asumsi sudah menggunakan Laravel UI)
Auth::routes();

// Routes untuk karyawan
Route::get('/api/karyawans/search', [KaryawanController::class, 'search'])->name('api.karyawans.search');

// Routes yang memerlukan autentikasi
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Resource routes
    Route::resource('karyawans', KaryawanController::class);
    // Mass delete employees
    Route::post('/karyawans/mass-delete', [KaryawanController::class, 'massDelete'])->name('karyawans.mass-delete');
    Route::resource('jenis-cutis', JenisCutiController::class);
    Route::resource('transportasis', TransportasiController::class);
    Route::resource('cutis', CutiController::class);

    // User management routes
    Route::resource('users', UserController::class);
    Route::post('/users/mass-delete', [UserController::class, 'massDelete'])->name('users.mass-delete');

    // Additional routes
    Route::patch('/cutis/{cuti}/approve', [CutiController::class, 'approve'])->name('cutis.approve');
    Route::patch('/cutis/{cuti}/reject', [CutiController::class, 'reject'])->name('cutis.reject');
    Route::patch('/cutis/{cuti}/update-ticket', [CutiController::class, 'updateTicketStatus'])->name('cutis.update-ticket');
    Route::get('/cutis-export', [CutiController::class, 'export'])->name('cutis.export');

    // Monitoring routes
    Route::get('/karyawans/{karyawan}/cuti-monitoring', [KaryawanController::class, 'cutiMonitoring'])
        ->name('karyawans.cuti-monitoring');

    // Route to manually refresh leave balances
    Route::get('/karyawans/{karyawan}/refresh-leave-balances', [KaryawanController::class, 'refreshLeaveBalances'])
        ->name('karyawans.refresh-leave-balances');

    // Monitoring Cuti dengan Tampilan Kalender
    Route::get('/cuti-calendar', [CutiController::class, 'calendarMonitoring'])
        ->name('cutis.calendar');

    // Export and Import routes
    Route::get('/karyawans-export', [KaryawanController::class, 'exportExcel'])
        ->name('karyawans.export');
    Route::get('/karyawans-template', [KaryawanController::class, 'exportTemplate'])
        ->name('karyawans.template');
    Route::post('/karyawans-import', [KaryawanController::class, 'importExcel'])
        ->name('karyawans.import');

    // Transportasi detail routes
    Route::get('/transportasi-details', [TransportasiDetailController::class, 'index'])
        ->name('transportasi_details.index');
    Route::get('/transportasi-details/export', [TransportasiDetailController::class, 'export'])
        ->name('transportasi_details.export');
    Route::get('/transportasi-details/dashboard', [TransportasiDetailController::class, 'dashboard'])
        ->name('transportasi_details.dashboard');
    Route::get('/transportasi-details/upcoming-deadlines', [TransportasiDetailController::class, 'upcomingDeadlines'])
        ->name('transportasi_details.upcoming_deadlines');
    Route::delete('/transportasi-details/batch-delete', [TransportasiDetailController::class, 'batchDelete'])
        ->name('transportasi_details.batch_delete');
    Route::get('/cutis/{cuti}/transportasi-details/create', [TransportasiDetailController::class, 'create'])
        ->name('transportasi_details.create');
    Route::post('/cutis/{cuti}/transportasi-details', [TransportasiDetailController::class, 'store'])
        ->name('transportasi_details.store');
    Route::get('/transportasi-details/{transportasiDetail}', [TransportasiDetailController::class, 'show'])
        ->name('transportasi_details.show');
    Route::get('/transportasi-details/{transportasiDetail}/edit', [TransportasiDetailController::class, 'edit'])
        ->name('transportasi_details.edit');
    Route::put('/transportasi-details/{transportasiDetail}', [TransportasiDetailController::class, 'update'])
        ->name('transportasi_details.update');
    Route::delete('/transportasi-details/{transportasiDetail}', [TransportasiDetailController::class, 'destroy'])
        ->name('transportasi_details.destroy');

    // Batch operations for Cuti
    Route::post('/cutis/batch-approve', [CutiController::class, 'batchApprove'])->name('cutis.batch-approve');
    Route::delete('/cutis/batch-delete', [CutiController::class, 'batchDelete'])->name('cutis.batch-delete');

    // Tambahkan alternatif route POST untuk batch-delete jika rute DELETE tidak berfungsi
    Route::post('/cutis/batch-delete-post', [CutiController::class, 'batchDeletePost'])->name('cutis.batch-delete-post');

    // Tambahkan alternatif route POST untuk batch-approve
    Route::post('/cutis/batch-approve-post', [CutiController::class, 'batchApprovePost'])->name('cutis.batch-approve-post');

    // Routes untuk import data cuti
    Route::get('/cuti-import', [CutiController::class, 'importForm'])
        ->name('cutis.import');
    Route::post('/cuti-import-process', [CutiController::class, 'processImport'])
        ->name('cutis.import.process');
    Route::get('/cuti-template-download', [CutiController::class, 'downloadTemplate'])
        ->name('cutis.template.download');

    // Routes untuk memo kompensasi
    Route::get('/memo-kompensasi', [MemoKompensasiController::class, 'index'])->name('memo-kompensasi.index');
    Route::put('/memo-kompensasi/{cuti}/update-status', [MemoKompensasiController::class, 'updateStatus'])->name('memo-kompensasi.update-status');
});

// Add at the end of the file
Route::get('/test-karyawan-search', function() {
    return view('test-karyawan-search');
});

// Test routes (remove in production)
Route::get('/test-api', function() {
    return view('test-api');
});