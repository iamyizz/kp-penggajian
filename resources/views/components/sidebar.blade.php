<aside class="d-flex flex-column flex-shrink-0 bg-white border-end shadow-sm" style="width: 250px; min-height: 100vh;">
    <!-- Header -->
    <div class="p-3 border-bottom bg-success text-white d-flex align-items-center">
        <i class="bi bi-bank2 me-2 fs-5"></i>
        <span class="fw-semibold fs-5">Klinik Samara</span>
    </div>

    <!-- Navigation -->
    <ul class="nav nav-pills flex-column mb-auto mt-2 px-2">
        @if (Auth::user()->role === 'admin')
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('jabatan.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('jabatan.*') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-briefcase me-2"></i> Jabatan
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('karyawan.index') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('karyawan.*') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-people me-2"></i> Data Karyawan
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('parameter.index') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('parameter.index') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-gear me-2"></i> Parameter Penggajian
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('tunjangan.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('tunjangan.*') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-cash-coin me-2"></i> Tunjangan Kehadiran & Makan
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('bonus.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('bonus.*') ? 'active bg-success text-white' : 'text-dark' }}">
                <i class="bi bi-gift me-2"></i> Bonus
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('penggajian.index') }}"
                class="nav-link d-flex align-items-center {{ request()->routeIs('penggajian.*') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-credit-card me-2"></i> Penggajian
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('laporan.index') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('laporan.*') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-file-earmark-text me-2"></i> Laporan
                </a>
            </li>
        @elseif (Auth::user()->role === 'koor_absen')
            <li class="nav-item mb-1">
                <a href="{{ route('dashboard') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('dashboard') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-house-door me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item mb-1">
                <a href="{{ route('absensi.index') }}"
                   class="nav-link d-flex align-items-center {{ request()->routeIs('absensi.*') ? 'active bg-success text-white' : 'text-dark' }}">
                    <i class="bi bi-calendar-check me-2"></i> Data Absensi
                </a>
            </li>
        @elseif (Auth::user()->role === 'owner')
            {{-- Approval Penggajian --}}
            <li class="nav-item mb-1">
                <a class="nav-link d-flex align-items-center {{ request()->routeIs('laporan.approvePage') ? 'active bg-success text-white' : 'text-dark' }}"
                href="{{ route('laporan.approvePage') }}">
                    <i class="bi bi-check2-square me-2"></i>
                    Approval Penggajian

                    @php
                        // Hitung jumlah periode (bulan-tahun) yang belum di-approve
                        $pendingCount = \App\Models\Penggajian::where('is_approved', false)
                            ->select('periode_bulan', 'periode_tahun')
                            ->groupBy('periode_bulan', 'periode_tahun')
                            ->get()
                            ->count();
                    @endphp

                    @if($pendingCount > 0)
                        <span class="badge bg-warning text-dark ms-auto">{{ $pendingCount }}</span>
                    @endif
                </a>
            </li>
        @endif
    </ul>
</aside>
