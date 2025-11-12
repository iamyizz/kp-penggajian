<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// ✅ Route Dashboard Dinamis (berdasarkan role)
Route::get('/dashboard', function () {
    $user = auth()->user();

    // Kalau user belum login, redirect ke login page
    if (!$user) {
        return redirect()->route('login');
    }

    return view('dashboard'); // hanya 1 view, isinya dinamis berdasarkan role
})->middleware(['auth', 'verified'])->name('dashboard');

// ✅ Route untuk Profile (default Breeze)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

// 🔹 Tambahkan route dummy untuk testing sidebar
Route::view('/karyawan', 'dashboard')->name('karyawan.index');
Route::view('/penggajian', 'dashboard')->name('penggajian.index');
Route::view('/bonus', 'dashboard')->name('bonus.index');
Route::view('/laporan', 'dashboard')->name('laporan.index');
Route::view('/absensi', 'dashboard')->name('absensi.index');
Route::view('/profil', 'dashboard')->name('profil');
