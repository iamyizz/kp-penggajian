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
                <div class="col-md-6">
                    <label class="form-label">Pilih Karyawan</label>
                    <select name="karyawan_id" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        @foreach($karyawans as $k)
                            <option value="{{ $k->id_karyawan }}">{{ $k->nip }} - {{ $k->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Bulan</label>
                    <select name="month" class="form-select">
                        @for($m=1;$m<=12;$m++)
<<<<<<< HEAD
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->locale('id')->translatedFormat('F') }}
                            </option>
=======
                            <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>{{ sprintf('%02d', $m) }}</option>
>>>>>>> 4e98af530a1c52172cbb55e67993dd36fbf28406
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tahun</label>
                    <input type="number" name="year" value="{{ $year }}" class="form-control" />
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary">Lihat</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
