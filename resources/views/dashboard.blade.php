@extends('layouts.app')

@section('content')

@php
    $isAdmin = auth()->user()->role === 'manajer';
    $isKoor = auth()->user()->role === 'staf_absen';
@endphp

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

    @if($isAdmin)
    {{-- Card Jabatan --}}
    <a href="{{ route('jabatan.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-briefcase class="w-8 h-8 text-indigo-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Jabatan</h2>
        <p class="text-gray-600 text-sm">Kelola data jabatan karyawan.</p>
    </a>

    {{-- Card Data Karyawan --}}
    <a href="{{ route('karyawan.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-users class="w-8 h-8 text-blue-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Data Karyawan</h2>
        <p class="text-gray-600 text-sm">Lihat data seluruh karyawan klinik.</p>
    </a>

    {{-- Card Parameter Penggajian --}}
    <a href="{{ route('parameter.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-cog-6-tooth class="w-8 h-8 text-gray-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Parameter Penggajian</h2>
        <p class="text-gray-600 text-sm">Atur parameter perhitungan gaji.</p>
    </a>

    {{-- Card Tunjangan Kehadiran & Makan --}}
    <a href="{{ route('tunjangan.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-gift class="w-8 h-8 text-pink-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Tunjangan Kehadiran & Makan</h2>
        <p class="text-gray-600 text-sm">Kelola tunjangan kehadiran dan makan.</p>
    </a>

    {{-- Card Bonus --}}
    <a href="{{ route('bonus.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-trophy class="w-8 h-8 text-yellow-500 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Bonus</h2>
        <p class="text-gray-600 text-sm">Kelola bonus kehadiran karyawan.</p>
    </a>

    {{-- Card Penggajian --}}
    <a href="{{ route('penggajian.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-credit-card class="w-8 h-8 text-green-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Penggajian</h2>
        <p class="text-gray-600 text-sm">Pantau dan kelola perhitungan gaji.</p>
    </a>

    {{-- Card Laporan --}}
    <a href="{{ route('laporan.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-document-chart-bar class="w-8 h-8 text-red-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Laporan</h2>
        <p class="text-gray-600 text-sm">Lihat laporan penggajian.</p>
    </a>
    @endif

    {{-- Card Absensi untuk Koor Absen --}}
    @if($isKoor)
    <a href="{{ route('absensi.index') }}"
       class="bg-white p-6 rounded-xl shadow hover:shadow-lg transition-all hover:scale-[1.02] block no-underline">
        <x-heroicon-s-calendar class="w-8 h-8 text-purple-600 mb-2" />
        <h2 class="font-semibold text-lg text-gray-900">Absensi</h2>
        <p class="text-gray-600 text-sm">Lihat kehadiran staf dan tenaga medis.</p>
    </a>
    @endif

</div>
@endsection
