@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg">
        <x-heroicon-s-users class="w-8 h-8 text-blue-600 mb-2" />
        <h2 class="font-semibold text-lg">Total Karyawan</h2>
        <p class="text-gray-600 text-sm">Lihat data seluruh karyawan klinik.</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg">
        <x-heroicon-s-credit-card class="w-8 h-8 text-green-600 mb-2" />
        <h2 class="font-semibold text-lg">Penggajian</h2>
        <p class="text-gray-600 text-sm">Pantau dan kelola perhitungan gaji.</p>
    </div>

    <div class="bg-white p-6 rounded-xl shadow hover:shadow-lg">
        <x-heroicon-s-calendar class="w-8 h-8 text-purple-600 mb-2" />
        <h2 class="font-semibold text-lg">Absensi</h2>
        <p class="text-gray-600 text-sm">Lihat kehadiran staf dan tenaga medis.</p>
    </div>
</div>
@endsection
