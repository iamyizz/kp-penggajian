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
                    <input type="month" name="bulan" value="{{ $bulan }}"
                        class="form-control rounded-pill">
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
            <table class="table table-hover table-striped align-middle mb-0">
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
                        <td class="text-center">{{ $loop->iteration }}</td>

                        <td>{{ $item->karyawan->nama }}</td>
                        <td>{{ $item->karyawan->jabatan->nama_jabatan }}</td>

                        <td class="text-end">{{ $item->total_hadir }}</td>
                        <td class="text-end">{{ $item->total_terlambat }}</td>

                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($item->nominal_bonus, 0, ',', '.') }}
                        </td>

                        <td class="text-center">
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
                        <div class="modal-dialog">
                            <div class="modal-content">

                                <div class="modal-header bg-success text-white">
                                    <h5 class="modal-title">Detail Bonus</h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <p><b>Nama:</b> {{ $item->karyawan->nama }}</p>
                                    <p><b>Total Hadir:</b> {{ $item->total_hadir }} hari</p>
                                    <p><b>Total Terlambat:</b> {{ $item->total_terlambat }} kali</p>
                                    <p><b>Bonus:</b> Rp {{ number_format($item->nominal_bonus) }}</p>
                                    <p><b>Status:</b> {{ $item->dapat_bonus ? 'Dapat' : 'Tidak' }}</p>
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
<script>
function formatBulan(bln, tahun) {
    const namaBulan = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    return namaBulan[parseInt(bln) - 1] + " " + tahun;
}

document.getElementById('btnProses').addEventListener('click', () => {

    const bulan = document.querySelector('input[name="bulan"]').value;
    if (!bulan) {
        return Swal.fire("Oops!", "Silakan pilih bulan dulu.", "warning");
    }

    const [tahun, bln] = bulan.split("-");

    Swal.fire({
        title: "Proses Bonus?",
        text: `Proses bonus bulan ${formatBulan(bln, tahun)}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Ya, proses",
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
                body: JSON.stringify({ bulan: bln, tahun })
            })
            .then(r => r.json())
            .then(r => {
                document.getElementById('loadingOverlay').style.display = 'none';
                Swal.fire({
                    icon: r.status ? "success" : "error",
                    title: r.message,
                    confirmButtonColor: '#198754',
                    customClass: {
                        popup: 'rounded-4',
                        confirmButton: 'rounded-pill px-4'
                    }
                }).then(() => location.reload());
            });
        }
    });
});

</script>
@endsection
