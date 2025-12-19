@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Rekap Absensi Bulanan</h2>
            <p class="small text-muted">Pilih karyawan dan bulan untuk melihat ringkasan absensi.</p>
        </div>
        <div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahDataModal">Tambah Data</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif


    <!-- Rekap Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('absensi.index') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Pilih Karyawan</label>
                    <select name="rekap_karyawan_id" class="form-select" required>
                        <option value="">-- Pilih Karyawan --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id_karyawan }}" {{ request('rekap_karyawan_id') == $k->id_karyawan ? 'selected' : '' }}>{{ $k->nip }} - {{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        @for($m=1;$m<=12;$m++)
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}" class="form-control" />
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lihat Rekap</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Rekap Results -->
    @if($karyawan && $rekapSummary)
        <div class="row mb-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <strong>{{ $karyawan->nip }} - {{ $karyawan->nama }}</strong> |
                    {{ \Carbon\Carbon::create()->month((int) $month)->locale('id')->translatedFormat('F') }} {{ $year }}
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Hadir</p>
                        <h5>{{ $rekapSummary['hadir'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Izin</p>
                        <h5>{{ $rekapSummary['izin'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Sakit</p>
                        <h5>{{ $rekapSummary['sakit'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Alpha</p>
                        <h5>{{ $rekapSummary['alpha'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Terlambat (count)</p>
                        <h5>{{ $rekapSummary['terlambat_count'] }} kali</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Terlambat (menit)</p>
                        <h5>{{ $rekapSummary['terlambat_minutes'] }} menit</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Pulang Cepat</p>
                        <h5>{{ $rekapSummary['pulang_cepat'] }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <p class="small text-muted mb-1">Total Jam Lembur</p>
                        <h5>{{ $rekapSummary['lembur_jam_total'] }} jam</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Table -->
        <div class="card">
            <div class="card-header">Detail Hari per Hari</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Jam Masuk</th>
                                <th>Jam Pulang</th>
                                <th>Status</th>
                                <th>Terlambat (menit)</th>
                                <th>Lembur (jam)</th>
                                <th>Pulang Cepat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rekapRecords as $r)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                                    <td>{{ $r->jam_masuk ?? '-' }}</td>
                                    <td>{{ $r->jam_keluar ?? '-' }}</td>
                                    <td>{{ $r->status_kehadiran }}</td>
                                    <td>
                                        @if($r->jam_masuk)
                                            @php
                                                $jamMasuk = \Carbon\Carbon::createFromFormat('H:i:s', $r->jam_masuk);
                                                $scheduledStart = \Carbon\Carbon::createFromFormat('H:i:s', $workStart);
                                                $diff = $scheduledStart->diffInMinutes($jamMasuk);
                                            @endphp
                                            {{ $diff }} menit
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ round($r->lembur_jam ?? 0) }} jam</td>
                                    <td>
                                        @if($r->jam_keluar)
                                            @php
                                                $jamKeluar = \Carbon\Carbon::createFromFormat('H:i:s', $r->jam_keluar);
                                                $scheduledEnd = \Carbon\Carbon::createFromFormat('H:i:s', $workEnd);
                                                $early = $jamKeluar->diffInMinutes($scheduledEnd);
                                                echo $early > 0 ? 'Ya' : 'Tidak';
                                            @endphp
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('absensi.edit', $r->id_kehadiran) }}" class="btn btn-sm btn-warning me-1" title="Edit">Edit</a>
                                        <form method="POST" action="{{ route('absensi.destroy', $r->id_kehadiran) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Hapus">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center small text-muted py-3">Tidak ada data untuk bulan ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Note/Hint -->
    <div class="mt-4">
        <small class="text-muted">
            <strong>Catatan:</strong> Gunakan form di atas untuk melihat rekap absensi bulanan. Klik tombol "Tambah Data" untuk menambah data absensi baru.
        </small>
    </div>
</div>

<!-- Modal Tambah Data -->
<div class="modal fade" id="tambahDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('absensi.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Karyawan</label>
                        <select name="karyawan_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id_karyawan }}">{{ $k->nip }} - {{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aksi</label>
                        <select name="action" class="form-select" required>
                            <option value="checkin">Ceklok Datang (Check-in)</option>
                            <option value="checkout">Ceklok Pulang (Check-out)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Placeholder for future scripts if needed
    // Any interactive JS for the Absensi index can be added here.
</script>
@endpush
