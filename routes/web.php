<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\KaryawanController;
use App\Http\Controllers\PenggajianController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ParameterPenggajianController;
use App\Http\Controllers\JabatanController;
use App\Http\Controllers\TunjanganController;
use App\Http\Controllers\BonusKehadiranController;

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
Route::middleware(['auth', 'role:manajer'])->group(function () {
    Route::resource('karyawan', KaryawanController::class);

    // routes untuk penggajian
    Route::post('/penggajian/proses', [PenggajianController::class, 'proses'])
        ->name('penggajian.proses');

    Route::resource('penggajian', PenggajianController::class)
        ->only(['index', 'show'])
        ->middleware(['auth','role:manajer']); // sesuaikan middleware

    // Routes Laporan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');

    // Detail Periode Laporan (untuk melihat data per karyawan pada periode tertentu)
    Route::get('/laporan/{tahun}/{bulan}', [LaporanController::class, 'periodeDetail'])
        ->whereNumber('tahun')
        ->whereNumber('bulan')
        ->name('laporan.periodeDetail');

    Route::prefix('parameter')->name('parameter.')->group(function () {
        Route::get('/', [ParameterPenggajianController::class, 'index'])->name('index');
        Route::post('/store', [ParameterPenggajianController::class, 'store'])->name('store');
        Route::put('/{id_param}/update', [ParameterPenggajianController::class, 'update'])->name('update');
        Route::delete('/{id_param}/delete', [ParameterPenggajianController::class, 'destroy'])->name('destroy');
    });

    Route::resource('jabatan', JabatanController::class);

    Route::post('/tunjangan-proses', [TunjanganController::class, 'proses'])
        ->name('tunjangan.proses');

    Route::resource('tunjangan', TunjanganController::class)
        ->only(['index']);


    // BONUS KEHADIRAN
    Route::post('/bonus-proses', [BonusKehadiranController::class, 'proses'])
        ->name('bonus.proses');

    Route::resource('bonus', BonusKehadiranController::class)
        ->only(['index']);

});

// ===========================
// 🧾 KOOR ABSEN ONLY
// ===========================
Route::middleware(['auth', 'role:staf_absen'])->group(function () {

    // ⬇️ Custom routes HARUS DI ATAS resource
    Route::get('/absensi/template', [AbsensiController::class, 'downloadTemplate'])->name('absensi.template');
    Route::post('/absensi/import', [AbsensiController::class, 'import'])->name('absensi.import');
    Route::get('/absensi-rekap', [AbsensiController::class, 'rekap'])->name('absensi.rekap');

    // ⬇️ Resource route DI BAWAH
    Route::resource('absensi', AbsensiController::class);
});


// ===========================
// 📄 SLIP PDF (Manajer & Direktur)
// ===========================
Route::middleware(['auth', 'role:manajer,direktur'])->group(function () {
    Route::get('/laporan/slip/{id}', [LaporanController::class, 'slipPdf'])->name('laporan.slipPdf');
});

// Routes Laporan (Owner only untuk approve)
Route::middleware(['auth', 'role:direktur'])->prefix('laporan')->group(function () {

    // Halaman Daftar Periode yang Perlu di-Approve
    Route::get('/approve', [LaporanController::class, 'approvePage'])->name('laporan.approvePage');

    // ✅ TAMBAHKAN INI - Detail Periode untuk Owner
    Route::get('/{tahun}/{bulan}/detail', [LaporanController::class, 'periodeDetail'])
        ->whereNumber('tahun')
        ->whereNumber('bulan')
        ->name('laporan.detail');

    // Proses Approve Periode
    Route::post('/{tahun}/{bulan}/approve', [LaporanController::class, 'approvePeriode'])
        ->whereNumber('tahun')
        ->whereNumber('bulan')
        ->name('laporan.approvePeriode');

    // Proses Reject/Batalkan Approve (opsional)
    Route::post('/{tahun}/{bulan}/reject', [LaporanController::class, 'rejectPeriode'])
        ->whereNumber('tahun')
        ->whereNumber('bulan')
        ->name('laporan.rejectPeriode');
});

// Route bawaan Breeze (login, register, dll)
require __DIR__ . '/auth.php';
