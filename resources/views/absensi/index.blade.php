@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">Absensi Harian</h4>
            <p class="small text-muted">Form ceklok masuk / pulang dan daftar hadir hari ini.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('absensi.rekap') }}" class="btn btn-outline-primary btn-sm">Rekap Bulanan</a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-body">
            <form method="POST" action="{{ route('absensi.store') }}" class="row g-2 align-items-end">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Pilih Karyawan</label>
                    <select name="karyawan_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id_karyawan }}">{{ $k->nip }} - {{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Aksi</label>
                    <select name="action" class="form-select">
                        <option value="checkin">Ceklok Datang (Check-in)</option>
                        <option value="checkout">Ceklok Pulang (Check-out)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success">Kirim</button>
                </div>
                <div class="col-md-12 mt-2">
                    <small class="text-muted">Jam kerja: {{ \Carbon\Carbon::parse($workStart)->format('H:i') }} - {{ \Carbon\Carbon::parse($workEnd)->format('H:i') }} | Batas terlambat: {{ $lateThreshold }} menit (cek-in setelah {{ \Carbon\Carbon::parse($workStart)->addMinutes($lateThreshold)->format('H:i') }} dianggap terlambat)</small>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">Daftar Hadir Hari Ini (<span id="current-date">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y') }}</span>) <small class="ms-3 text-muted">Waktu Indonesia (WIB):</small> <span id="current-time" data-work-start="{{ $workStart }}" data-late-threshold="{{ $lateThreshold }}" class="ms-2 fw-bold"></span></div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                            <th>Terlambat</th>
                            <th>Lembur (jam)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendances as $a)
                            <tr>
                                <td>{{ $a->karyawan->nip ?? '-' }}</td>
                                <td>{{ $a->karyawan->nama ?? '-' }}</td>
                                <td>{{ $a->jam_masuk ?? '-' }}</td>
                                <td>{{ $a->jam_keluar ?? '-' }}</td>
                                <td>{{ $a->status_kehadiran }}</td>
                                <td>{{ $a->terlambat ? 'Ya' : 'Tidak' }}</td>
                                <td>{{ $a->lembur_jam }}</td>
                                <td>
                                    @if(auth()->check() && in_array(auth()->user()->role, ['admin','koor_absen']))
                                        <form action="{{ route('absensi.destroy', $a->id_kehadiran) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data absensi ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center small text-muted">Belum ada data hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Simple real-time clock for the absensi page (showing Asia/Jakarta time)
    function updateClock(){
        const el = document.getElementById('current-time');
        if(!el) return;

        const fmt = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' });
        const parts = fmt.formatToParts(new Date());
        const hour = parseInt(parts.find(p => p.type === 'hour').value, 10);
        const minute = parseInt(parts.find(p => p.type === 'minute').value, 10);
        const second = parseInt(parts.find(p => p.type === 'second').value, 10);
        const hh = hour.toString().padStart(2,'0');
        const mm = minute.toString().padStart(2,'0');
        const ss = second.toString().padStart(2,'0');
        el.textContent = `${hh}:${mm}:${ss}`;

        // Visual hint: mark if current Jakarta time is past late threshold
        const workStart = el.dataset.workStart || '{{ $workStart }}';
        const lateThreshold = parseInt(el.dataset.lateThreshold || '{{ $lateThreshold }}', 10);
        const [wsH, wsM] = (workStart || '08:00:00').split(':').map(n => parseInt(n,10));

        const nowMinutes = hour * 60 + minute;
        const thresholdMinutes = (wsH * 60 + wsM) + lateThreshold;

        if(nowMinutes > thresholdMinutes){
            el.classList.add('text-danger');
            el.title = 'Waktu melewati batas terlambat (cek-in setelah ini akan dianggap terlambat)';
        } else {
            el.classList.remove('text-danger');
            el.title = '';
        }
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
@endpush
