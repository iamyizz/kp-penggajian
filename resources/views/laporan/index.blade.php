@extends('layouts.app')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">💰 Penggajian Karyawan</h2>
            <p class="text-muted small mb-0">Perhitungan gaji lengkap berdasarkan data jabatan, tunjangan & potongan</p>
        </div>

    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Bulan</label>
                        @php
                            // Jika $bulan bernilai "2025-08"
                            $bulanValue = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);
                        @endphp
                        <input type="text" id="bulan_filter" class="form-control"
                            placeholder="Pilih Bulan & Tahun"
                            name="bulan"
                            value="{{ $bulanValue }}">
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-success px-4 shadow-sm rounded-pill">
                            <i class="bi bi-search"></i> Filter
                        </button>
                    </div>
            </form>
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
                <h4>{{ number_format($totalLembur,0,',','.') }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-white" style="background: #e74a3b;">
                <h6>Total Potongan</h6>
                <h4>{{ number_format($totalPotongan,0,',','.') }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm p-3 text-white" style="background: #f6c23e;">
                <h6>Total Biaya Gaji</h6>
                <h4>{{ number_format($totalBiayaGaji,0,',','.') }}</h4>
            </div>
        </div>
    </div>



    {{-- Tabel Data Gaji --}}
    <div class="card shadow-sm mt-4">
        @php
            use Carbon\Carbon;
            Carbon::setLocale('id');
        @endphp

        <div class="card-header">
            <strong>
                Data Gaji Bulan
                {{ Carbon::create()->month($bulan)->translatedFormat('F') }}
                {{ $tahun }}
            </strong>
        </div>


        <div class="card-body table-responsive">
            <table class="table table-hover table-striped align-middle text-center mb-0">
                <thead class="table-success text-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Potongan</th>
                        <th>Total Gaji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($data as $gaji)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $gaji->karyawan->nama }}</td>
                        <td>{{ $gaji->karyawan->jabatan->nama_jabatan ?? '-' }}</td>
                        <td>Rp {{ number_format($gaji->gaji_pokok,0,',','.') }}</td>
                        <td>Rp {{ number_format($gaji->tunjangan_jabatan + $gaji->tunjangan_kehadiran_makan,0,',','.') }}</td>
                        <td>Rp {{ number_format($gaji->potongan_absen + $gaji->potongan_bpjs,0,',','.') }}</td>
                        <td>Rp {{ number_format($gaji->total_gaji,0,',','.') }}</td>

                        <td>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#detail{{ $gaji->id_penggajian }}">
                                <i class="bi bi-info-circle"></i> Detail
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>

</div>


{{-- =====================================================
     MODAL DITARUH DI LUAR TABLE — FIX UTAMA!
===================================================== --}}
@foreach($data as $item)
    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="detail{{ $item->id_penggajian }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content border-0 shadow-lg">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        Detail Laporan Gaji — {{ $item->karyawan->nama }}
                    </h5>
                    <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    {{-- ========================= --}}
                    {{-- 1. INFORMASI KARYAWAN     --}}
                    {{-- ========================= --}}
                    <h5 class="fw-bold mb-3">Informasi Karyawan</h5>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><th>Nama</th><td>{{ $item->karyawan->nama }}</td></tr>
                                <tr><th>NIP</th><td>{{ $item->karyawan->nip ?? '-' }}</td></tr>
                                <tr><th>Jenis Kelamin</th><td>{{ $item->karyawan->jenis_kelamin ?? '-' }}</td></tr>
                                <tr><th>Status</th><td>{{ $item->karyawan->status_karyawan ?? 'Tetap' }}</td></tr>
                            </table>
                        </div>

                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr><th>Jabatan</th><td>{{ $item->karyawan->jabatan->nama_jabatan }}</td></tr>
                                <tr><th>Tanggal Masuk</th><td>{{ $item->karyawan->tanggal_masuk ?? '-' }}</td></tr>
                                <tr><th>Masa Kerja</th>
                                    <td>
                                        @php
                                            if ($item->karyawan->tanggal_masuk) {
                                                $mulai = new DateTime($item->karyawan->tanggal_masuk);
                                                $now = new DateTime();
                                                $interval = $mulai->diff($now);
                                                echo $interval->y . ' Tahun ' . $interval->m . ' Bulan';
                                            } else echo '-';
                                        @endphp
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>



                    {{-- ========================= --}}
                    {{-- 2. GAJI POKOK & TUNJANGAN --}}
                    {{-- ========================= --}}
                    <h5 class="fw-bold mt-4">Rincian Gaji</h5>

                    <table class="table table-bordered">
                        <tr class="table-light"><th colspan="2">Gaji Pokok</th></tr>
                        <tr>
                            <td>Gaji Pokok</td>
                            <td class="text-end fw-bold">Rp {{ number_format($item->gaji_pokok) }}</td>
                        </tr>

                        <tr class="table-light"><th colspan="2">Tunjangan</th></tr>
                        @php
                            $tkm = $tunjanganList[$item->karyawan_id] ?? null;
                        @endphp

                        <tr><td>Tunjangan Jabatan</td><td class="text-end">Rp {{ number_format($item->tunjangan_jabatan) }}</td></tr>
                        <tr><td>Tunjangan Kehadiran</td><td class="text-end">Rp {{ number_format($tkm->tunjangan_harian) }}</td></tr>
                        <tr class="table-danger"><td>Potongan Terlambat</td><td class="text-end">Rp {{ number_format($tkm->potongan_terlambat) }}</td></tr>

                        <tr class="table-info fw-bold">
                            <td>Total Tunjangan</td>
                            <td class="text-end">
                                Rp {{ number_format(
                                    $item->tunjangan_jabatan +
                                    $item->tunjangan_kehadiran_makan
                                ) }}
                            </td>
                        </tr>
                    </table>



                    {{-- ========================= --}}
                    {{-- 3. DETAIL ABSENSI         --}}
                    {{-- ========================= --}}
                    <h5 class="fw-bold mt-4">Rincian Kehadiran</h5>

                    @php
                        $abs = $dataAbsensi[$item->id_penggajian];
                    @endphp

                    <table class="table table-bordered text-center">
                        <tr>
                            <th>Hadir</th>
                            <th>Izin</th>
                            <th>Sakit</th>
                            <th>Alpa</th>
                            <th>Terlambat</th>
                            <th>Lembur (Jam)</th>
                        </tr>
                        <tr>
                            <td>{{ $abs['hadir'] }}</td>
                            <td>{{ $abs['izin'] }}</td>
                            <td>{{ $abs['sakit'] }}</td>
                            <td>{{ $abs['alpa'] }}</td>
                            <td>{{ $abs['terlambat'] }}</td>
                            <td>{{ $abs['lembur_jam'] }}</td>
                        </tr>
                    </table>

                    {{-- ========================= --}}
                    {{-- 4. RINCIAN POTONGAN       --}}
                    {{-- ========================= --}}
                    <h5 class="fw-bold mt-4">Rincian Potongan</h5>

                    <table class="table table-bordered">
                        <tr><td>Potongan Absen</td><td class="text-end">Rp {{ number_format($item->potongan_absen) }}</td></tr>
                        <tr><td>Potongan BPJS</td><td class="text-end">Rp {{ number_format($item->potongan_bpjs) }}</td></tr>
                        <tr><td>Potongan Lain-lain</td><td class="text-end">Rp {{ number_format($item->potongan_lain ?? 0) }}</td></tr>

                        <tr class="table-danger fw-bold">
                            <td>Total Potongan</td>
                            <td class="text-end">
                                Rp {{ number_format(
                                    $item->potongan_absen +
                                    $item->potongan_bpjs +
                                    ($item->potongan_terlambat ?? 0) +
                                    ($item->potongan_lain ?? 0)
                                ) }}
                            </td>
                        </tr>
                    </table>

                    {{-- ========================= --}}
                    {{-- 5. RINGKASAN GAJI         --}}
                    {{-- ========================= --}}
                    @php
                        $totalTunjangan =
                            $item->tunjangan_jabatan +
                            $item->tunjangan_kehadiran_makan +
                            ($item->tunjangan_transport ?? 0) +
                            ($item->tunjangan_keluarga ?? 0);

                        $totalPotongan =
                            $item->potongan_absen +
                            $item->potongan_bpjs +
                            ($item->potongan_terlambat ?? 0) +
                            ($item->potongan_lain ?? 0);

                        $totalLembur = $item->lembur ?? 0;   // ← TAMBAHKAN INI

                        // Gaji Bersih = Pokok + Tunjangan + Lembur
                        $gajiBersih = $item->gaji_pokok + $totalTunjangan + $totalLembur;

                        // THP = Gaji Bersih - Potongan
                        $takeHomePay = $gajiBersih - $totalPotongan;
                    @endphp

                    <h5 class="fw-bold mt-4">Ringkasan Gaji</h5>

                    <table class="table table-bordered">
                        <tr>
                            <th>Gaji Pokok</th>
                            <td class="text-end">Rp {{ number_format($item->gaji_pokok) }}</td>
                        </tr>

                        <tr>
                            <th>Total Tunjangan</th>
                            <td class="text-end">Rp {{ number_format($totalTunjangan) }}</td>
                        </tr>

                        <tr>
                            <th>Lembur</th>
                            <td class="text-end">Rp {{ number_format($totalLembur) }}</td>
                        </tr>

                        <tr class="table-info fw-bold">
                            <th>Gaji Bersih</th>
                            <td class="text-end">Rp {{ number_format($gajiBersih) }}</td>
                        </tr>

                        <tr>
                            <th>Total Potongan</th>
                            <td class="text-end">Rp {{ number_format($totalPotongan) }}</td>
                        </tr>

                        <tr class="table-success fw-bold">
                            <th>Take Home Pay</th>
                            <td class="text-end">Rp {{ number_format($takeHomePay) }}</td>
                        </tr>
                    </table>

                    <p class="mt-2 text-muted">
                        Periode Gaji:
                        <strong>{{ date('F Y', strtotime($item->periode_tahun.'-'.$item->periode_bulan.'-01')) }}</strong>
                    </p>

                    {{-- ========================= --}}
                    {{-- 6. TAKE HOME PAY          --}}
                    {{-- ========================= --}}
                    <div class="mt-4 p-3 bg-success text-white rounded">
                        <small>Total Take Home Pay</small>
                        <h2 class="fw-bold">Rp {{ number_format($item->total_gaji) }}</h2>
                    </div>

                    <p class="mt-2 text-muted">
                        Periode Gaji:
                        <strong>{{ date('F Y', strtotime($item->periode_tahun.'-'.$item->periode_bulan.'-01')) }}</strong>
                    </p>



                    {{-- ========================= --}}
                    {{-- 7. Export / TTD           --}}
                    {{-- ========================= --}}
                    <div class="d-flex justify-content-between align-items-center mt-4">

                        <div>
                            <a href="{{ url('/laporan/slip/' . $item->id_penggajian) }}"
                            class="btn btn-danger rounded-pill px-4">
                                <i class="bi bi-file-earmark-pdf-fill"></i> Slip PDF
                            </a>
                        </div>

                        <div class="text-end">
                            <p class="mb-1"><strong>{{ Auth::user()->name ?? 'Admin' }}</strong></p>
                            <small class="text-muted">HRD / Finance</small>
                        </div>

                    </div>

                </div> {{-- modal body end --}}
            </div>
        </div>
    </div>
@endforeach
{{-- SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
<script>
    flatpickr("#bulan_filter", {
        altInput: true,
        altFormat: "F Y",
        dateFormat: "Y-m",
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y" }) ]
    });
</script>
@endsection
