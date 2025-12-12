@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">💰 Penggajian Karyawan</h2>
            <p class="text-muted small mb-0">Perhitungan gaji lengkap berdasarkan data jabatan, tunjangan & potongan</p>
        </div>

        <button id="btnProsesGaji"
            class="btn btn-success shadow-sm px-4 rounded-pill">
            <i class="bi bi-calculator me-1"></i> Proses Penggajian
        </button>
    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-semibold">Bulan</label>
                    @php
                        // Jika $bulan bernilai "2025-08"
                        $bulanValue = $bulan;
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

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle text-center mb-0">
                <thead class="table-success text-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Lembur</th>
                        <th>Potongan</th>
                        <th>Total Gaji</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->karyawan->nama }}</td>
                        <td>{{ $item->karyawan->jabatan->nama_jabatan }}</td>

                        <td>Rp {{ number_format($item->gaji_pokok) }}</td>
                        <td>
                            Rp {{ number_format(
                                $item->tunjangan_jabatan +
                                $item->tunjangan_kehadiran_makan
                                ) }}
                        </td>
                        <td>Rp {{ number_format($item->lembur) }}</td>

                        <td class="fw-bold text-danger">
                            Rp {{ number_format(
                                $item->potongan_absen +
                                $item->potongan_bpjs
                            ) }}
                        </td>

                        <td class="fw-bold text-success">
                            Rp {{ number_format($item->total_gaji) }}
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#detail{{ $item->id_penggajian }}">
                                <i class="bi bi-info-circle"></i> Detail
                            </button>
                        </td>
                    </tr>

                    {{-- DETAIL MODAL --}}
                    <div class="modal fade" id="detail{{ $item->id_penggajian }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg border-0">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Detail Penggajian</h5>
                                    <button type="button" class="btn-close btn-close-white"
                                        data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <h5 class="mb-1">{{ $item->karyawan->nama }}</h5>
                                    <small class="text-muted">{{ $item->karyawan->jabatan->nama_jabatan }}</small>

                                    <div class="row g-3 mt-3">

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Gaji Pokok</small>
                                                <h5>Rp {{ number_format($item->gaji_pokok) }}</h5>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Tunjangan Jabatan</small>
                                                <h5>Rp {{ number_format($item->tunjangan_jabatan) }}</h5>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Tunjangan Kehadiran</small>
                                                <h5>Rp {{ number_format($item->tunjangan_kehadiran_makan) }}</h5>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Lembur</small>
                                                <h5>Rp {{ number_format($item->lembur) }}</h5>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Potongan Absen</small>
                                                <h5>Rp {{ number_format($item->potongan_absen) }}</h5>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Potongan BPJS</small>
                                                <h5>Rp {{ number_format($item->potongan_bpjs) }}</h5>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="mt-4 p-3 rounded bg-success text-white">
                                        <small>Total Gaji</small>
                                        <h3 class="mb-0">Rp {{ number_format($item->total_gaji) }}</h3>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="8" class="text-muted py-4">
                            <i class="bi bi-inboxes fs-3"></i>
                            <div class="mt-2">Belum ada data penggajian.</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>

    {{-- MODAL PROSES --}}
    <div class="modal fade" id="modalProsesGaji" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Proses Penggajian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label class="form-label fw-semibold">Pilih Bulan & Tahun</label>
                    <input type="text" id="bulan_tahun"
                        class="form-control glass-input"
                        placeholder="Pilih Bulan & Tahun">
                </div>

                <div class="modal-footer border-0">
                    <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button id="btnModalProsesGaji" class="btn btn-primary rounded-pill px-4">
                        Proses
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- OVERLAY LOADING --}}
<div id="loadingOverlay"
    style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:9999;backdrop-filter:blur(3px);">
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <div class="spinner-border" style="width:4rem;height:4rem;"></div>
        <p class="mt-3 fs-5">Memproses...</p>
    </div>
</div>

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
                altFormat: "F Y"
            })
        ]
    });

    flatpickr("#bulan_tahun", {
        altInput: true,
        altFormat: "F Y",
        dateFormat: "Y-m",
        plugins: [
            new monthSelectPlugin({
                shorthand: true,
                dateFormat: "Y-m",
                altFormat: "F Y"
            })
        ]
    });

    function formatBulan(bln, tahun) {
        const namaBulan = [
            "Januari", "Februari", "Maret", "April", "Mei", "Juni",
            "Juli", "Agustus", "September", "Oktober", "November", "Desember"
        ];
        return namaBulan[parseInt(bln) - 1] + " " + tahun;
    }

    // buka modal proses
    document.getElementById('btnProsesGaji').onclick = () => {
        new bootstrap.Modal('#modalProsesGaji').show();
    };

    // proses penggajian
    document.getElementById('btnModalProsesGaji').onclick = () => {

        const fp = document.querySelector("#bulan_tahun")._flatpickr;

        if (!fp) return Swal.fire("Oops!", "Picker tidak ditemukan!", "error");

        const raw = fp.input.value;  // format Y-m -> contoh: 2025-08

        if (!raw) return Swal.fire("Oops!", "Pilih bulan dahulu!", "warning");

        const [tahun, bulan] = raw.split('-');

        Swal.fire({
            title: "Proses Penggajian?",
            text: `Proses gaji bulan ${formatBulan(bulan, tahun)}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#0d6efd"
        }).then(res => {
            if (!res.isConfirmed) return;

            document.getElementById('loadingOverlay').style.display = "block";

            fetch("{{ route('penggajian.proses') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ bulan, tahun })
            })
            .then(r => r.json())
            .then(r => {
                document.getElementById('loadingOverlay').style.display = "none";
                Swal.fire({
                    icon: r.status ? "success" : "error",
                    title: r.message,
                }).then(() => location.reload());
            });
        });
    };
</script>

@endsection
