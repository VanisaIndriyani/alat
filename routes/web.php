<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReturnsController;
use App\Http\Controllers\LoansController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\SettingsController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/peminjaman', [LoansController::class, 'index'])->name('loans');
    Route::post('/peminjaman/search', [LoansController::class, 'search'])->name('loans.search');
    Route::post('/peminjaman/cart/add', [LoansController::class, 'addToCart'])->name('loans.cart.add');
    Route::post('/peminjaman/cart/remove', [LoansController::class, 'removeFromCart'])->name('loans.cart.remove');
    Route::post('/peminjaman/process', [LoansController::class, 'process'])->name('loans.process');
    Route::get('/pengembalian', [ReturnsController::class, 'index'])->name('returns');
    Route::post('/pengembalian/check', [ReturnsController::class, 'check'])->name('returns.check');
    Route::post('/pengembalian/process', [ReturnsController::class, 'process'])->name('returns.process');
    Route::get('/peralatan', [EquipmentController::class, 'index'])->name('equipment');
    Route::get('/peralatan/qr/print', [EquipmentController::class, 'printAll'])->name('equipment.qr.print');
    Route::get('/peralatan/{id}/qr', [EquipmentController::class, 'printSingle'])->name('equipment.qr.single');
    Route::post('/peralatan', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::post('/peralatan/{id}', [EquipmentController::class, 'update'])->name('equipment.update');
    Route::post('/peralatan/{id}/delete', [EquipmentController::class, 'destroy'])->name('equipment.delete');
    Route::get('/laporan', [\App\Http\Controllers\ReportsController::class, 'index'])->name('reports');
    Route::get('/laporan/export', [\App\Http\Controllers\ReportsController::class, 'exportExcel'])->name('reports.export');
    Route::get('/laporan/print', [\App\Http\Controllers\ReportsController::class, 'printView'])->name('reports.print');
    Route::get('/pengaturan', [SettingsController::class, 'index'])->name('settings');
    Route::post('/pengaturan/instansi', [SettingsController::class, 'saveInstitution'])->name('settings.institution.save');
    Route::post('/pengaturan/petugas', [SettingsController::class, 'staffStore'])->name('settings.staff.store');
    Route::post('/pengaturan/petugas/{id}', [SettingsController::class, 'staffUpdate'])->name('settings.staff.update');
    Route::post('/pengaturan/petugas/{id}/account', [SettingsController::class, 'staffCreateAccount'])->name('settings.staff.account');
    Route::post('/pengaturan/petugas/{id}/delete', [SettingsController::class, 'staffDelete'])->name('settings.staff.delete');
    Route::post('/pengaturan/peminjam', [SettingsController::class, 'studentStore'])->name('settings.student.store');
    Route::post('/pengaturan/peminjam/{id}', [SettingsController::class, 'studentUpdate'])->name('settings.student.update');
    Route::post('/pengaturan/peminjam/{id}/delete', [SettingsController::class, 'studentDelete'])->name('settings.student.delete');
    Route::get('/pengaturan/peminjam/find', [SettingsController::class, 'studentFind'])->name('settings.student.find');
});
