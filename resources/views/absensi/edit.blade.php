@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">Edit Data Absensi</h4>
            <p class="small text-muted">Ubah jam masuk/pulang atau status untuk record ini.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('absensi.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('absensi.update', $attendance->{ 'id_kehadiran' }) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Karyawan</label>
                    <input type="text" readonly class="form-control" value="{{ $attendance->karyawan->nip }} - {{ $attendance->karyawan->nama }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="text" readonly class="form-control" value="{{ $attendance->tanggal }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Status Kehadiran</label>
                    <select name="status_kehadiran" class="form-select">
                        <option value="Hadir" {{ $attendance->status_kehadiran == 'Hadir' ? 'selected' : '' }}>Hadir</option>
                        <option value="Izin" {{ $attendance->status_kehadiran == 'Izin' ? 'selected' : '' }}>Izin</option>
                        <option value="Sakit" {{ $attendance->status_kehadiran == 'Sakit' ? 'selected' : '' }}>Sakit</option>
                        <option value="Alpa" {{ $attendance->status_kehadiran == 'Alpa' ? 'selected' : '' }}>Alpa</option>
                    </select>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Masuk</label>
                        <input type="time" name="jam_masuk" class="form-control" value="{{ $attendance->jam_masuk ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->jam_masuk)->format('H:i') : '' }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Jam Pulang</label>
                        <input type="time" name="jam_keluar" class="form-control" value="{{ $attendance->jam_keluar ? \Carbon\Carbon::createFromFormat('H:i:s', $attendance->jam_keluar)->format('H:i') : '' }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Lembur (jam)</label>
                    <input type="number" step="0.01" name="lembur_jam" class="form-control" value="{{ $attendance->lembur_jam ?? 0 }}">
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('absensi.index') }}" class="btn btn-secondary me-2">Batal</a>
                    <button class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
