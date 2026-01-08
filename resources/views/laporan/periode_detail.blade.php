@extends('layouts.app')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            @if (Auth::user()->role === 'owner')
                <a href="{{ route('laporan.approvePage') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @else
                <a href="{{ route('laporan.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            @endif
            <h2 class="fw-bold text-success mb-0">
                📋 Laporan Penggajian — {{ $namaBulan }} {{ $tahun }}
            </h2>
            <p class="text-muted small mb-0">Detail penggajian per karyawan pada periode ini</p>
        </div>
        <div>
            @if($ringkasan['is_approved'])
                <span class="badge bg-success fs-6 py-2 px-3">
                    <i class="bi bi-check-circle"></i> Approved
                </span>
            @else
                <span class="badge bg-warning text-dark fs-6 py-2 px-3">
                    <i class="bi bi-clock"></i> Pending Approval
                </span>
            @endif
        </div>
    </div>

    {{-- Ringkasan Periode --}}
    <div class="row g-3 mb-4 justify-content-center">
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-3">
                    <div class="text-primary fs-2 fw-bold">{{ $ringkasan['total_karyawan'] }}</div>
                    <small class="text-muted">Karyawan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-3">
                    <div class="text-secondary fw-bold">Rp {{ number_format($ringkasan['total_gaji_pokok'], 0, ',', '.') }}</div>
                    <small class="text-muted">Gaji Pokok</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-3">
                    <div class="text-info fw-bold">Rp {{ number_format($ringkasan['total_tunjangan'], 0, ',', '.') }}</div>
                    <small class="text-muted">Tunjangan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-3">
                    <div class="text-success fw-bold">Rp {{ number_format($ringkasan['total_lembur'] + $ringkasan['total_bonus'], 0, ',', '.') }}</div>
                    <small class="text-muted">Lembur + Bonus</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-center py-3">
                    <div class="text-danger fw-bold">Rp {{ number_format($ringkasan['total_potongan'], 0, ',', '.') }}</div>
                    <small class="text-muted">Potongan</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <div class="card shadow-sm border-0 h-100 bg-success text-white">
                <div class="card-body text-center py-3">
                    <div class="fw-bold">Rp {{ number_format($ringkasan['total_biaya_gaji'], 0, ',', '.') }}</div>
                    <small>Total Gaji</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail Per Karyawan --}}
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-white py-3">
            <strong><i class="bi bi-table me-2"></i>Detail Penggajian Per Karyawan</strong>
            <div>
                @if(Auth::user()->role === 'owner' && !$ringkasan['is_approved'])
                    <form action="{{ route('laporan.approvePeriode', [$tahun, $bulan]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm"
                                onclick="return confirm('Yakin ingin meng-approve periode ini?')">
                            <i class="bi bi-check-lg"></i> Approve Periode Ini
                        </button>
                    </form>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 1000px;">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center py-3" style="width: 50px;">No</th>
                            <th class="py-3" style="min-width: 200px;">Karyawan</th>
                            <th class="text-end py-3" style="min-width: 120px;">Gaji Pokok</th>
                            <th class="text-end py-3" style="min-width: 120px;">Tunjangan</th>
                            <th class="text-end py-3" style="min-width: 100px;">Lembur</th>
                            <th class="text-end py-3" style="min-width: 100px;">Bonus</th>
                            <th class="text-end py-3" style="min-width: 110px;">Potongan</th>
                            <th class="text-end py-3" style="min-width: 130px;">Total Gaji</th>
                            <th class="text-center py-3" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data as $gaji)
                        @php
                            $totalTunjangan = $gaji->tunjangan_jabatan + $gaji->tunjangan_kehadiran_makan;
                            $totalPotongan = $gaji->potongan_absen + $gaji->potongan_bpjs;
                        @endphp
                        <tr>
                            <td class="text-center text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                        <i class="bi bi-person text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-semibold">{{ $gaji->karyawan->nama }}</div>
                                        <small class="text-muted">
                                            {{ $gaji->karyawan->jabatan->nama_jabatan ?? '-' }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="text-nowrap">Rp {{ number_format($gaji->gaji_pokok, 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end">
                                <span class="text-nowrap text-info">Rp {{ number_format($totalTunjangan, 0, ',', '.') }}</span>
                                <br>
                                <small class="text-muted" data-bs-toggle="tooltip"
                                       title="Jabatan: Rp {{ number_format($gaji->tunjangan_jabatan, 0, ',', '.') }} | Kehadiran: Rp {{ number_format($gaji->tunjangan_kehadiran_makan, 0, ',', '.') }}">
                                </small>
                            </td>
                            <td class="text-end">
                                @if($gaji->lembur > 0)
                                    <span class="text-nowrap text-success">+Rp {{ number_format($gaji->lembur, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($gaji->bonus > 0)
                                    <span class="text-nowrap text-success">+Rp {{ number_format($gaji->bonus, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($totalPotongan > 0)
                                    <span class="text-nowrap text-danger">-Rp {{ number_format($totalPotongan, 0, ',', '.') }}</span>
                                    <br>
                                    <small class="text-muted" data-bs-toggle="tooltip"
                                           title="Absen: Rp {{ number_format($gaji->potongan_absen, 0, ',', '.') }} | BPJS: Rp {{ number_format($gaji->potongan_bpjs, 0, ',', '.') }}">
                                    </small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <span class="text-nowrap fw-bold text-success fs-6">
                                    Rp {{ number_format($gaji->total_gaji, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detail{{ $gaji->id_penggajian }}"
                                            title="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    <a href="{{ route('laporan.slipPdf', $gaji->id_penggajian) }}"
                                       class="btn btn-outline-danger"
                                       title="Download PDF">
                                        <i class="bi bi-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-light">
                        <tr class="fw-bold">
                            <td colspan="2" class="text-end py-3">TOTAL</td>
                            <td class="text-end py-3">
                                <span class="text-nowrap">Rp {{ number_format($data->sum('gaji_pokok'), 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end py-3 text-info">
                                <span class="text-nowrap">Rp {{ number_format($data->sum('tunjangan_jabatan') + $data->sum('tunjangan_kehadiran_makan'), 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end py-3 text-success">
                                <span class="text-nowrap">Rp {{ number_format($data->sum('lembur'), 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end py-3 text-success">
                                <span class="text-nowrap">Rp {{ number_format($data->sum('bonus'), 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end py-3 text-danger">
                                <span class="text-nowrap">Rp {{ number_format($data->sum('potongan_absen') + $data->sum('potongan_bpjs'), 0, ',', '.') }}</span>
                            </td>
                            <td class="text-end py-3 text-success">
                                <span class="text-nowrap fs-6">Rp {{ number_format($data->sum('total_gaji'), 0, ',', '.') }}</span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PER KARYAWAN --}}
@foreach($data as $item)
<div class="modal fade" id="detail{{ $item->id_penggajian }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-badge"></i> Slip Gaji — {{ $item->karyawan->nama }}
                </h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                {{-- Info Karyawan --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Nama Karyawan</td>
                                <td class="fw-semibold">: {{ $item->karyawan->nama }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jabatan</td>
                                <td class="fw-semibold">: {{ $item->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">NIP</td>
                                <td class="fw-semibold">: {{ $item->karyawan->nip ?? '-' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted" style="width: 140px;">Periode</td>
                                <td class="fw-semibold">: {{ $namaBulan }} {{ $tahun }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Tanggal Cetak</td>
                                <td class="fw-semibold">: {{ now()->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    @if($ringkasan['is_approved'])
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                {{-- Detail Pendapatan & Potongan --}}
                <div class="row">
                    {{-- Kolom Pendapatan --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-success mb-3">
                            <i class="bi bi-plus-circle me-1"></i> Pendapatan
                        </h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Gaji Pokok</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->gaji_pokok, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Jabatan</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->tunjangan_jabatan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Tunjangan Kehadiran & Makan</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->tunjangan_kehadiran_makan, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Lembur</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->lembur, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Bonus</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->bonus, 0, ',', '.') }}</td>
                                </tr>
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="fw-bold text-success">
                                    <td>Total Pendapatan</td>
                                    <td class="text-end">
                                        @php
                                            $totalPendapatan = $item->gaji_pokok + $item->tunjangan_jabatan + $item->tunjangan_kehadiran_makan + $item->lembur + $item->bonus;
                                        @endphp
                                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    {{-- Kolom Potongan --}}
                    <div class="col-md-6">
                        <h6 class="fw-bold text-danger mb-3">
                            <i class="bi bi-dash-circle me-1"></i> Potongan
                        </h6>
                        <table class="table table-sm">
                            <tbody>
                                <tr>
                                    <td>Potongan Absen</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->potongan_absen, 0, ',', '.') }}</td>
                                </tr>
                                <tr>
                                    <td>Potongan BPJS</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->potongan_bpjs, 0, ',', '.') }}</td>
                                </tr>
                                {{-- Tambahkan potongan lain jika ada --}}
                                @if(isset($item->potongan_lainnya) && $item->potongan_lainnya > 0)
                                <tr>
                                    <td>Potongan Lainnya</td>
                                    <td class="text-end fw-medium">Rp {{ number_format($item->potongan_lainnya, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </tbody>
                            <tfoot class="border-top">
                                <tr class="fw-bold text-danger">
                                    <td>Total Potongan</td>
                                    <td class="text-end">
                                        @php
                                            $totalPotonganModal = $item->potongan_absen + $item->potongan_bpjs + ($item->potongan_lainnya ?? 0);
                                        @endphp
                                        Rp {{ number_format($totalPotonganModal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <hr>

                {{-- Total Gaji Bersih --}}
                <div class="bg-success bg-opacity-10 rounded-3 p-3 mt-3">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="mb-0 text-muted">GAJI BERSIH (Take Home Pay)</h6>
                        </div>
                        <div class="col-auto">
                            <h4 class="mb-0 fw-bold text-success">
                                Rp {{ number_format($item->total_gaji, 0, ',', '.') }}
                            </h4>
                        </div>
                    </div>
                </div>

                {{-- Catatan (jika ada) --}}
                @if(isset($item->catatan) && $item->catatan)
                <div class="alert alert-light border mt-3 mb-0">
                    <small class="text-muted">
                        <i class="bi bi-info-circle me-1"></i> <strong>Catatan:</strong> {{ $item->catatan }}
                    </small>
                </div>
                @endif
            </div>

            <div class="modal-footer bg-light">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i> Tutup
                </button>
                <a href="{{ route('laporan.slipPdf', $item->id_penggajian) }}" class="btn btn-danger">
                    <i class="bi bi-file-pdf me-1"></i> Download PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection

@push('scripts')
<script>
    // Inisialisasi Tooltip Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Fungsi Print Slip
    function printSlip(id) {
        var modalContent = document.querySelector('#detail' + id + ' .modal-body').innerHTML;
        var printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Slip Gaji</title>
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
                <style>
                    body { padding: 20px; font-size: 14px; }
                    @media print {
                        .no-print { display: none; }
                        body { padding: 0; }
                    }
                    .header-print {
                        text-align: center;
                        border-bottom: 2px solid #198754;
                        padding-bottom: 15px;
                        margin-bottom: 20px;
                    }
                    .header-print h4 { color: #198754; margin-bottom: 5px; }
                </style>
            </head>
            <body>
                <div class="header-print">
                    <h4><i class="bi bi-building"></i> NAMA PERUSAHAAN</h4>
                    <small class="text-muted">Alamat Perusahaan, Kota, Kode Pos</small>
                </div>
                <h5 class="text-center mb-4 fw-bold">SLIP GAJI KARYAWAN</h5>
                ${modalContent}
                <div class="mt-4 text-center no-print">
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak
                    </button>
                    <button class="btn btn-secondary" onclick="window.close()">
                        <i class="bi bi-x-lg"></i> Tutup
                    </button>
                </div>
                <div class="mt-5 pt-4 border-top text-center">
                    <small class="text-muted">
                        Dokumen ini dicetak secara elektronik dan sah tanpa tanda tangan.
                    </small>
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
@endpush

@push('styles')
<style>
    /* Custom styling untuk tabel yang lebih rapi */
    .table > :not(caption) > * > * {
        padding: 0.75rem 1rem;
    }

    /* Hover effect yang lebih halus */
    .table-hover > tbody > tr:hover {
        background-color: rgba(25, 135, 84, 0.05);
    }

    /* Badge styling yang lebih modern */
    .badge {
        font-weight: 500;
    }

    /* Card shadow yang lebih subtle */
    .card.shadow-sm {
        box-shadow: 0 0.125rem 0.5rem rgba(0, 0, 0, 0.075) !important;
    }

    /* Responsive table scrollbar styling */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    /* Modal styling */
    .modal-content {
        border-radius: 0.75rem;
    }

    .modal-header {
        border-radius: 0.75rem 0.75rem 0 0;
    }
</style>
@endpush
