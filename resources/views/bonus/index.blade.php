@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">🎁 Bonus Kehadiran</h2>
            <p class="text-muted small mb-0">Perhitungan bonus berdasarkan kedisiplinan dan kehadiran karyawan</p>
        </div>

        <button id="btnProses"
            class="btn btn-success shadow-sm px-4 rounded-pill">
            <i class="bi bi-cpu me-1"></i> Proses Bonus
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
                    <input type="text" id="bulan_filter"
                        class="form-control glass-input"
                        placeholder="Pilih Bulan & Tahun"
                        value="{{ $bulanValue }}"
                        name="bulan">
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
                    <tr class="text-center">
                        <th>#</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Total Hadir</th>
                        <th>Total Terlambat</th>
                        <th>Bonus</th>
                        <th>Status</th>
                        <th width="10%">Aksi</th>
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

                        <td class="fw-bold text-success">
                            Rp {{ number_format($item->nominal_bonus, 0, ',', '.') }}
                        </td>

                        <td>
                            @if($item->dapat_bonus)
                                <span class="badge bg-success px-3 py-2 rounded-pill">Dapat</span>
                            @else
                                <span class="badge bg-secondary px-3 py-2 rounded-pill">Tidak</span>
                            @endif
                        </td>

                        <td>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3"
                                data-bs-toggle="modal"
                                data-bs-target="#detail{{ $item->id }}">
                                <i class="bi bi-info-circle"></i> Detail
                            </button>
                        </td>
                    </tr>
                    <div class="modal fade" id="detail{{ $item->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content shadow-lg border-0">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Detail Bonus Karyawan</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    <!-- Nama -->
                                    <div class="mb-3">
                                        <h5 class="mb-1">{{ $item->karyawan->nama }}</h5>
                                        <span class="badge
                                            {{ $item->dapat_bonus ? 'bg-success' : 'bg-danger' }}">
                                            {{ $item->dapat_bonus ? 'Mendapat Bonus' : 'Tidak Dapat Bonus' }}
                                        </span>
                                    </div>

                                    <!-- Informasi Bonus -->
                                    <div class="row g-3">

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Total Hadir</small>
                                                <h5 class="mb-0">{{ $item->total_hadir }} hari</h5>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Terlambat</small>
                                                <h5 class="mb-0">{{ $item->total_terlambat }} kali</h5>
                                            </div>
                                        </div>

                                        @if(!empty($item->total_sakit))
                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Sakit</small>
                                                <h5 class="mb-0">{{ $item->total_sakit }} hari</h5>
                                            </div>
                                        </div>
                                        @endif

                                        @if(!empty($item->total_izin))
                                        <div class="col-6">
                                            <div class="p-3 border rounded bg-light">
                                                <small class="text-muted">Izin</small>
                                                <h5 class="mb-0">{{ $item->total_izin }} hari</h5>
                                            </div>
                                        </div>
                                        @endif

                                    </div>

                                    <!-- Bonus -->
                                    <div class="mt-4 p-3 rounded bg-success text-white">
                                        <small>Nominal Bonus</small>
                                        <h4 class="mb-0">
                                            Rp {{ number_format($item->nominal_bonus) }}
                                        </h4>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>

                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <i class="bi bi-inboxes fs-3"></i>
                            <div class="mt-2">Belum ada data bonus bulan ini.</div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>

            </table>
        </div>
    </div>
    <!-- MODAL PROSES BONUS -->
    <div class="modal fade" id="modalProsesBonus" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">

            <div class="modal-header bg-success text-white rounded-top-4">
                <h5 class="modal-title">
                    <i class="bi bi-cash-coin me-2"></i> Proses Bonus Kehadiran
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
                <button id="btnModalProsesBonus" class="btn btn-success rounded-pill px-4">
                    Proses
                </button>
            </div>

        </div>
    </div>
    </div>
</div>


{{-- LOADING OVERLAY --}}
<div id="loadingOverlay"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); z-index:9999;
    backdrop-filter:blur(3px);">
    <div class="position-absolute top-50 start-50 translate-middle text-center text-white">
        <div class="spinner-border" style="width:4rem; height:4rem;"></div>
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

    // buka modal
    document.getElementById('btnProses').addEventListener('click', () => {
        const modal = new bootstrap.Modal('#modalProsesBonus');
        modal.show();
    });

    document.getElementById('btnModalProsesBonus').addEventListener('click', () => {

        const bulanInput = document.getElementById('bulan_tahun').value;

        if (!bulanInput) {
            return Swal.fire("Oops!", "Silakan pilih bulan untuk diproses!", "warning");
        }

        const [tahun, bulan] = bulanInput.split('-');

        Swal.fire({
            title: "Proses Bonus?",
            text: `Proses bonus kehadiran bulan ${formatBulan(bulan, tahun)}?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonText: "Ya, proses",
            cancelButtonText: "Batal",
            confirmButtonColor: "#198754"
        }).then(res => {
            if (res.isConfirmed) {

                document.getElementById('loadingOverlay').style.display = 'block';

                fetch("{{ route('bonus.proses') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ bulan, tahun })
                })
                .then(r => r.json())
                .then(r => {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    Swal.fire({
                        icon: r.status ? "success" : "error",
                        title: r.message,
                        confirmButtonColor: '#198754',
                    }).then(() => location.reload());
                });
            }
        });

    });
</script>
@endsection
