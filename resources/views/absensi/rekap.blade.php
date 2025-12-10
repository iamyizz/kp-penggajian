 @extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">Rekap Absensi Bulanan</h4>
            <p class="small text-muted">Pilih karyawan dan bulan untuk melihat ringkasan absensi.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('absensi.rekap') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Pilih Karyawan</label>
                    <select name="karyawan_id" class="form-select" required>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id_karyawan }}" @if($k->id_karyawan == $karyawan->id_karyawan) selected @endif>{{ $k->nip }} - {{ $k->nama }}</option>
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
                    <button class="btn btn-primary">Lihat Rekap</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Hadir</p>
                    <h5>{{ $summary['hadir'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Izin</p>
                    <h5>{{ $summary['izin'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Sakit</p>
                    <h5>{{ $summary['sakit'] }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Alpha</p>
                    <h5>{{ $summary['alpha'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Terlambat (count)</p>
                    <h5>{{ $summary['terlambat_count'] }}</h5>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Terlambat (menit)</p>
                    <h5>{{ $summary['terlambat_minutes'] }}</h5>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <p class="small text-muted mb-1">Total Pulang Cepat</p>
                    <h5>{{ $summary['pulang_cepat'] }}</h5>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
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
                            <th>Pulang Cepat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $r)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($r->tanggal)->format('d M Y') }}</td>
                                <td>{{ $r->jam_masuk ?? '-' }}</td>
                                <td>{{ $r->jam_keluar ?? '-' }}</td>
                                <td>{{ $r->status_kehadiran }}</td>
                                <td>
                                    @if($r->jam_masuk)
                                        @php
                                            $diff = \Carbon\Carbon::createFromFormat('H:i:s', $r->jam_masuk)->diffInMinutes(\Carbon\Carbon::createFromFormat('H:i:s', config('attendance.work_start')));
                                            echo $diff > 0 ? $diff : 0;
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($r->jam_keluar)
                                        @php
                                            $early = \Carbon\Carbon::createFromFormat('H:i:s', config('attendance.work_end'))->diffInMinutes(\Carbon\Carbon::createFromFormat('H:i:s', $r->jam_keluar));
                                            echo $early > 0 ? 'Ya' : 'Tidak';
                                        @endphp
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center small text-muted">Tidak ada data untuk bulan ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
