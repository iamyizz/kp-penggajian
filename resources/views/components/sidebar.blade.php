<aside class="w-64 bg-white shadow-lg flex flex-col">
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center space-x-2">
            <x-heroicon-s-banknotes class="w-6 h-6 text-green-600" />
            <span class="text-lg font-semibold">Klinik Samara</span>
        </div>
    </div>

    <nav class="flex-1 p-4 space-y-1">
        @if (Auth::user()->role === 'admin')
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-home class="w-5 h-5 text-gray-600" />
                <span>Dashboard</span>
            </a>
            <a href="{{ route('karyawan.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-users class="w-5 h-5 text-gray-600" />
                <span>Data Karyawan</span>
            </a>
            <a href="{{ route('penggajian.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-credit-card class="w-5 h-5 text-gray-600" />
                <span>Penggajian</span>
            </a>
            <a href="{{ route('bonus.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-gift class="w-5 h-5 text-gray-600" />
                <span>Bonus</span>
            </a>
            <a href="{{ route('laporan.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-document-text class="w-5 h-5 text-gray-600" />
                <span>Laporan</span>
            </a>
        @elseif (Auth::user()->role === 'koor_absen')
            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-home class="w-5 h-5 text-gray-600" />
                <span>Dashboard</span>
            </a>
            <a href="{{ route('absensi.index') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-calendar class="w-5 h-5 text-gray-600" />
                <span>Data Absensi</span>
            </a>
            <a href="{{ route('profil') }}" class="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-100">
                <x-heroicon-s-user class="w-5 h-5 text-gray-600" />
                <span>Profil</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-gray-200 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} Klinik Samara
    </div>
</aside>
