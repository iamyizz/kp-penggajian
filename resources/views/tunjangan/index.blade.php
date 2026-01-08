@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">🍱 Tunjangan Kehadiran & Makan</h2>
            <p class="text-muted small mb-0">Perhitungan tunjangan berdasarkan kehadiran karyawan</p>
        </div>

        <button id="btnProses" class="btn btn-success shadow-sm px-4 rounded-pill">
            <i class="bi bi-cpu me-1"></i> Proses Tunjangan
        </button>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted">Periode Bulan</label>
                    @php
                        // Jika $bulan bernilai "2025-08"
                        $bulanValue = $bulan;
                    @endphp
                    <input type="text" id="bulan_filter"
                        class="form-control glass-input"
                        placeholder="Pilih Bulan & Tahun"
                        value="{{ $bulanValue }}"
                        name="bulan">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-search me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover align-middle text-center mb-0">
                <thead class="table-success">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Total Hadir</th>
                        <th>Total Terlambat</th>
                        <th>Subtotal Tunjangan</th>
                        <th>Potongan</th>
                        <th>Total Tunjangan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($data as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $item->karyawan->nama }}</td>
                        <td>{{ $item->karyawan->jabatan->nama_jabatan }}</td>

                        <td>{{ $item->total_hadir }}</td>
                        <td>{{ $item->total_terlambat }}</td>

                        <td>Rp {{ number_format($item->tunjangan_harian,0,',','.') }}</td>
                        <td class="text-danger">Rp {{ number_format($item->potongan_terlambat,0,',','.') }}</td>

                        <td class="fw-bold text-success">
                            Rp {{ number_format($item->total_tunjangan,0,',','.') }}
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#detail{{ $item->id_tkm }}">
                                <i class="bi bi-info-circle"></i> Detail
                            </button>
                        </td>
                    </tr>
                    <!-- Modal Detail -->
                    <div class="modal fade" id="detail{{ $item->id_tkm }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">

                                <div class="modal-header bg-success text-white rounded-top-4">
                                    <h5 class="modal-title fw-semibold">
                                        <i class="bi bi-cash-stack me-2"></i> Detail Tunjangan
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <p class="fw-bold mb-1">{{ $item->karyawan->nama }}</p>
                                    <p class="text-muted small mb-3">{{ $item->karyawan->jabatan->nama_jabatan }}</p>

                                    <div class="border rounded-4 p-3 mb-3 bg-light">
                                        <h6 class="fw-bold text-success mb-2">Tunjangan Kehadiran & Makan</h6>

                                        <div class="d-flex justify-content-between">
                                            <span>Total Hadir:</span>
                                            <span>{{ $item->total_hadir }} hari</span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span>Tarif per Hari:</span>
                                            <span>Rp {{ number_format($tarif_makan,0,',','.') }}</span>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between fw-semibold">
                                            <span>Total Tunjangan:</span>
                                            <span>Rp {{ number_format($item->tunjangan_harian,0,',','.') }}</span>
                                        </div>
                                    </div>

                                    <div class="border rounded-4 p-3 mb-3 bg-light">
                                        <h6 class="fw-bold text-danger mb-2">Potongan Keterlambatan</h6>

                                        <div class="d-flex justify-content-between">
                                            <span>Total Terlambat:</span>
                                            <span>{{ $item->total_terlambat }} kali</span>
                                        </div>

                                        <div class="d-flex justify-content-between">
                                            <span>Potongan per Kali:</span>
                                            <span>Rp {{ number_format($tarif_potongan,0,',','.') }}</span>
                                        </div>

                                        <hr>

                                        <div class="d-flex justify-content-between fw-semibold text-danger">
                                            <span>Total Potongan:</span>
                                            <span>Rp {{ number_format($item->potongan_terlambat,0,',','.') }}</span>
                                        </div>
                                    </div>

                                    <div class="border rounded-4 p-3 bg-white shadow-sm">
                                        <div class="d-flex justify-content-between fw-bold">
                                            <span>Total Akhir:</span>
                                            <span class="text-success">
                                                Rp {{ number_format($item->total_tunjangan,0,',','.') }}
                                            </span>
                                        </div>
                                    </div>

                                </div>

                                <div class="modal-footer border-0">
                                    <button class="btn btn-outline-secondary rounded-pill"
                                        data-bs-dismiss="modal">Tutup</button>
                                </div>

                            </div>
                        </div>
                    </div>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-database-x fs-1"></i>
                                <p class="mt-3 mb-0">Belum ada data untuk periode ini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal Pilih Bulan Proses -->
    <div class="modal fade" id="modalProses" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title">
                    <i class="bi bi-calendar-check me-2"></i>
                    Proses Tunjangan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <label class="form-label fw-semibold">Pilih Bulan & Tahun</label>
                    <input type="text" id="bulan_tahun"
                        class="form-control glass-input"
                        placeholder="Pilih Bulan & Tahun"
                        name="bulan">
            </div>

            <div class="modal-footer border-0">
                <button class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button id="btnModalProses" class="btn btn-success rounded-pill px-4">Proses</button>
            </div>

            </div>
        </div>
    </div>
</div>

{{-- Overlay Loading --}}
<div id="loadingOverlay"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99999; backdrop-filter:blur(3px);">
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <div class="spinner-border" style="width:4rem; height:4rem;"></div>
        <p class="mt-3 fs-5">Memproses...</p>
    </div>
</div>

{{-- Script --}}
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

    document.getElementById('btnProses').addEventListener('click', () => {
        const modal = new bootstrap.Modal('#modalProses');
        modal.show();
    });

    document.getElementById('btnModalProses').addEventListener('click', () => {
        const bulanInput = document.getElementById('bulan_tahun').value;

        if (!bulanInput) {
            return Swal.fire("Oops!", "Silakan pilih bulan terlebih dahulu.", "warning");
        }

        const [tahun, bulan] = bulanInput.split("-");

        Swal.fire({
            title: "Proses Tunjangan?",
            text: `Proses tunjangan bulan ${formatBulan(bulan, tahun)}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, proses",
            cancelButtonText: "Batal",
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d'
        }).then(result => {
            if (result.isConfirmed) {
                document.getElementById('loadingOverlay').style.display = 'block';

                fetch("{{ route('tunjangan.proses') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ bulan, tahun })
                })
                .then(res => res.json())
                .then(res => {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    Swal.fire({
                        icon: res.status ? "success" : "error",
                        title: res.status ? "Berhasil!" : "Gagal",
                        text: res.message,
                    }).then(() => {
                        // ✅ Auto-filter: redirect dengan parameter bulan
                        if (res.status && res.redirect_filter) {
                            window.location.href = "{{ route('tunjangan.index') }}?bulan=" + res.redirect_filter;
                        } else {
                            location.reload();
                        }
                    });
                })
                .catch(err => {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    Swal.fire("Error!", "Terjadi kesalahan saat memproses.", "error");
                });
            }
        });
    });

</script>

@endsection
