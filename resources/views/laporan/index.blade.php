@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">💰 Laporan Penggajian</h2>
            <p class="text-muted small mb-0">Ringkasan penggajian per periode</p>
        </div>
    </div>

    {{-- Dashboard Mini --}}
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-white" style="background: #4e73df;">
                <h6>Total Karyawan</h6>
                <h4>{{ $totalKaryawan }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-white" style="background: #1cc88a;">
                <h6>Total Lembur</h6>
                <h4>Rp {{ number_format($totalLembur, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-white" style="background: #e74a3b;">
                <h6>Total Potongan</h6>
                <h4>Rp {{ number_format($totalPotongan, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-white" style="background: #f6c23e;">
                <h6>Total Biaya Gaji</h6>
                <h4>Rp {{ number_format($totalBiayaGaji, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    {{-- Tabel Data Penggajian Per Periode --}}
    <div class="card shadow-sm mt-4">
        @php
            use Carbon\Carbon;
            Carbon::setLocale('id');
        @endphp

        <div class="card-header">
            <strong>Data Penggajian Per Periode</strong>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle text-center mb-0">
                <thead class="table-success text-dark">
                    <tr>
                        <th>No</th>
                        <th>Periode</th>
                        <th>Total Karyawan</th>
                        <th>Total Lembur</th>
                        <th>Total Potongan</th>
                        <th>Total Biaya Gaji</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $periode)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>
                            <strong>
                                {{ Carbon::create()->month($periode->periode_bulan)->translatedFormat('F') }}
                                {{ $periode->periode_tahun }}
                            </strong>
                        </td>
                        <td>
                            <span class="badge bg-primary rounded-pill">
                                {{ $periode->total_karyawan }} Orang
                            </span>
                        </td>
                        <td>Rp {{ number_format($periode->total_lembur, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($periode->total_potongan, 0, ',', '.') }}</td>
                        <td>
                            <strong class="text-success">
                                Rp {{ number_format($periode->total_biaya_gaji, 0, ',', '.') }}
                            </strong>
                        </td>
                        <td>
                            @if($periode->is_approved)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle"></i> Approved
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td>
                            <a class="btn btn-sm btn-outline-success rounded-pill"
                               href="{{ route('laporan.periodeDetail', [$periode->periode_tahun, $periode->periode_bulan]) }}">
                               <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-muted py-4">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            Belum ada data penggajian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
