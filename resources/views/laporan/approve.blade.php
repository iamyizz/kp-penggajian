@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-1">
                <i class="bi bi-check2-square me-2"></i>Approval Penggajian
            </h2>
            <p class="text-muted mb-0">Kelola persetujuan periode penggajian karyawan</p>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Ringkasan Status --}}
    <div class="row g-3 mb-4 justify-content-center">
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-warning mb-1">{{ $pendingApproval->count() }}</h3>
                    <small class="text-muted">Menunggu Approval</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-check-circle text-success fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-success mb-1">{{ $approvedList->count() }}</h3>
                    <small class="text-muted">Sudah Disetujui</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-4">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar-check text-primary fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-1">{{ $pendingApproval->count() + $approvedList->count() }}</h3>
                    <small class="text-muted">Total Periode</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Pending Approval --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning bg-opacity-10 py-3">
            <h5 class="mb-0 fw-semibold text-warning">
                <i class="bi bi-hourglass-split me-2"></i>Periode Menunggu Approval
            </h5>
        </div>
        <div class="card-body p-0">
            @if($pendingApproval->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted">Periode</th>
                            <th class="px-4 py-3 text-muted text-center">Karyawan</th>
                            <th class="px-4 py-3 text-muted text-end">Total Gaji Pokok</th>
                            <th class="px-4 py-3 text-muted text-end">Total Tunjangan</th>
                            <th class="px-4 py-3 text-muted text-end">Total Potongan</th>
                            <th class="px-4 py-3 text-muted text-end">Total Biaya Gaji</th>
                            <th class="px-4 py-3 text-muted text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingApproval as $periode)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-event text-warning"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $periode->nama_bulan }} {{ $periode->tahun }}</div>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>Menunggu persetujuan
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                    {{ $periode->total_karyawan }} Orang
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="text-nowrap">Rp {{ number_format($periode->total_gaji_pokok, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="text-nowrap text-info">Rp {{ number_format($periode->total_tunjangan, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="text-nowrap text-danger">Rp {{ number_format($periode->total_potongan, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="text-nowrap fw-bold text-success fs-6">
                                    Rp {{ number_format($periode->total_biaya_gaji, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group">
                                    <a href="{{ route('laporan.detail', ['tahun' => $periode->periode_tahun, 'bulan' => $periode->periode_bulan]) }}"
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal{{ $periode->tahun }}{{ $periode->bulan }}"
                                            title="Approve Periode">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Konfirmasi Approve --}}
                        <div class="modal fade" id="approveModal{{ $periode->tahun }}{{ $periode->bulan }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-success text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-check-circle me-2"></i>Konfirmasi Approval
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body text-center py-4">
                                        <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                            <i class="bi bi-question-lg text-success fs-1"></i>
                                        </div>
                                        <h5 class="fw-bold mb-2">Approve Periode Ini?</h5>
                                        <p class="text-muted mb-3">
                                            Anda akan menyetujui penggajian periode:<br>
                                            <strong class="text-dark fs-5">{{ $periode->nama_bulan }} {{ $periode->tahun }}</strong>
                                        </p>
                                        <div class="bg-light rounded-3 p-3 text-start">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <small class="text-muted">Total Karyawan</small>
                                                    <div class="fw-semibold">{{ $periode->total_karyawan }} Orang</div>
                                                </div>
                                                <div class="col-6">
                                                    <small class="text-muted">Total Biaya Gaji</small>
                                                    <div class="fw-semibold text-success">Rp {{ number_format($periode->total_biaya_gaji, 0, ',', '.') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="alert alert-warning mt-3 mb-0 text-start">
                                            <small>
                                                <i class="bi bi-exclamation-triangle me-1"></i>
                                                <strong>Perhatian:</strong> Setelah di-approve, data penggajian periode ini tidak dapat diubah lagi.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="modal-footer justify-content-center border-0 pt-0">
                                        <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                            <i class="bi bi-x-lg me-1"></i>Batal
                                        </button>
                                        <form action="{{ route('laporan.approvePeriode', [$periode->periode_tahun, $periode->periode_bulan]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success px-4">
                                                <i class="bi bi-check-lg me-1"></i>Ya, Approve
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-check-all text-success fs-1"></i>
                </div>
                <h5 class="text-muted">Tidak Ada Periode Pending</h5>
                <p class="text-muted mb-0">Semua periode penggajian sudah di-approve.</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Tabel Periode Sudah Approved --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-success bg-opacity-10 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold text-success">
                <i class="bi bi-check-circle me-2"></i>Periode Sudah Disetujui
            </h5>
            <span class="badge bg-success">{{ $approvedList->count() }} Periode</span>
        </div>
        <div class="card-body p-0">
            @if($approvedList->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted">Periode</th>
                            <th class="px-4 py-3 text-muted text-center">Karyawan</th>
                            <th class="px-4 py-3 text-muted text-end">Total Biaya Gaji</th>
                            <th class="px-4 py-3 text-muted">Disetujui Oleh</th>
                            <th class="px-4 py-3 text-muted">Tanggal Approval</th>
                            <th class="px-4 py-3 text-muted text-center">Status</th>
                            <th class="px-4 py-3 text-muted text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($approvedList as $periode)
                        <tr>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-calendar-check text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $periode->nama_bulan }} {{ $periode->tahun }}</div>
                                        <small class="text-muted">Periode Penggajian</small>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-primary-subtle text-primary px-3 py-2">
                                    {{ $periode->total_karyawan }} Orang
                                </span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <span class="text-nowrap fw-bold text-success">
                                    Rp {{ number_format($periode->total_biaya_gaji, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center">
                                    <div class="bg-secondary bg-opacity-10 rounded-circle p-2 me-2">
                                        <i class="bi bi-person text-secondary"></i>
                                    </div>
                                    <span class="fw-medium">{{ $periode->approver ?? 'Owner' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($periode->approved_at)
                                    <div class="fw-medium">{{ \Carbon\Carbon::parse($periode->approved_at)->format('d M Y') }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($periode->approved_at)->format('H:i') }} WIB</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Approved
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="btn-group">
                                    <a href="{{ route('laporan.detail', [$periode->periode_tahun, $periode->periode_bulan]) }}"
                                       class="btn btn-sm btn-outline-primary" title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#rejectModal{{ $periode->periode_tahun }}{{ $periode->periode_bulan }}"
                                            title="Batalkan Approval">
                                        <i class="bi bi-x-lg"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Konfirmasi Batalkan Approval --}}
                        <div class="modal fade" id="rejectModal{{ $periode->periode_tahun }}{{ $periode->periode_bulan }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content border-0 shadow">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">
                                            <i class="bi bi-x-circle me-2"></i>Batalkan Approval
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('laporan.rejectPeriode', [$periode->periode_tahun, $periode->periode_bulan]) }}" method="POST">
                                        @csrf
                                        <div class="modal-body py-4">
                                            <div class="text-center mb-4">
                                                <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                    <i class="bi bi-exclamation-triangle text-danger fs-1"></i>
                                                </div>
                                                <h5 class="fw-bold mb-2">Batalkan Approval?</h5>
                                                <p class="text-muted mb-0">
                                                    Periode: <strong class="text-dark">{{ $periode->nama_bulan }} {{ $periode->periode_tahun }}</strong>
                                                </p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-medium">Alasan Pembatalan <span class="text-muted">(opsional)</span></label>
                                                <textarea name="alasan" class="form-control" rows="3"
                                                          placeholder="Masukkan alasan pembatalan approval..."></textarea>
                                            </div>
                                            <div class="alert alert-danger mb-0">
                                                <small>
                                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                                    <strong>Perhatian:</strong> Membatalkan approval akan mengubah status periode menjadi pending kembali.
                                                </small>
                                            </div>
                                        </div>
                                        <div class="modal-footer justify-content-center border-0 pt-0">
                                            <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">
                                                <i class="bi bi-arrow-left me-1"></i>Kembali
                                            </button>
                                            <button type="submit" class="btn btn-danger px-4">
                                                <i class="bi bi-x-lg me-1"></i>Ya, Batalkan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                    <i class="bi bi-inbox text-muted fs-1"></i>
                </div>
                <h5 class="text-muted">Belum Ada Periode Approved</h5>
                <p class="text-muted mb-0">Periode yang sudah di-approve akan tampil di sini.</p>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    .table > :not(caption) > * > * {
        padding: 0.875rem 1rem;
    }

    .table-hover > tbody > tr:hover {
        background-color: rgba(25, 135, 84, 0.04);
    }

    .card {
        border-radius: 0.75rem;
    }

    .card-header {
        border-radius: 0.75rem 0.75rem 0 0 !important;
    }

    .modal-content {
        border-radius: 0.75rem;
    }

    .modal-header {
        border-radius: 0.75rem 0.75rem 0 0;
    }

    .btn-group .btn {
        border-radius: 0.375rem !important;
        margin: 0 2px;
    }
</style>
@endpush
