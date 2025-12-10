<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\BonusController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ParameterPenggajianController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root ke login
Route::get('/', function () {
    return redirect()->route('login');
});

// Dashboard (1 view dinamis berdasarkan role)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if (!$user) {
        return redirect()->route('login');
    }

    return view('dashboard'); // view tunggal, isi dinamis berdasar role
})->middleware(['auth', 'verified'])->name('dashboard');

// Default Breeze (profile)
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===========================
// 👑 ADMIN ONLY
// ===========================
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('karyawan', KaryawanController::class);
    Route::resource('penggajian', GajiController::class);
    Route::resource('bonus', BonusController::class);
    Route::resource('laporan', LaporanController::class);

    // Absensi routes (admin dapat akses)
    Route::resource('absensi', AbsensiController::class);
    Route::get('absensi-rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');

    Route::prefix('parameter')->name('parameter.')->group(function () {
        Route::get('/', [ParameterPenggajianController::class, 'index'])->name('index');
        Route::post('/store', [ParameterPenggajianController::class, 'store'])->name('store');
        Route::put('/{id_param}/update', [ParameterPenggajianController::class, 'update'])->name('update');
        Route::delete('/{id_param}/delete', [ParameterPenggajianController::class, 'destroy'])->name('destroy');
    });
});

// ===========================
// 🧾 KOOR ABSEN ONLY
// ===========================
Route::middleware(['auth', 'role:koor_absen'])->group(function () {
    Route::resource('absensi', AbsensiController::class);
    Route::get('absensi-rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');
});

// Route bawaan Breeze (login, register, dll)
require __DIR__ . '/auth.php';
