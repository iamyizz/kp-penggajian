@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">⚙ Parameter Penggajian</h2>
            <p class="text-muted small mb-0">Atur nilai dasar untuk perhitungan penggajian klinik</p>
        </div>
        <button class="btn btn-success shadow-sm px-3 rounded-pill"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Parameter
        </button>
    </div>

    {{-- Alert --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Table --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <table class="table table-hover table-striped align-middle text-center mb-0">
                <thead class="table-success text-dark">
                    <tr>
                        <th width="15%">Key</th>
                        <th>Nama Parameter</th>
                        <th width="15%">Nilai</th>
                        <th width="10%">Satuan</th>
                        <th>Keterangan</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($params as $p)
                        <tr>
                            <td class="fw-semibold text-secondary">{{ $p->key }}</td>
                            <td>{{ $p->nama_param }}</td>
                            <td>{{ $p->nilai }}</td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ $p->satuan ?? '-' }}
                                </span>
                            </td>
                            <td class="text-muted small">{{ $p->keterangan ?? '-' }}</td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    {{-- Edit --}}
                                    <button class="btn btn-sm btn-outline-warning px-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEdit{{ $p->id_param }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger px-2 btn-delete"
                                            data-id="{{ $p->id_param }}"
                                            data-nama="{{ $p->nama_param }}"
                                            data-url="{{ route('parameter.destroy', $p->id_param) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $p->id_param }}"
                                        action="{{ route('parameter.destroy', $p->id_param) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $p->id_param }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered modal-lg">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                            <form method="POST" action="{{ route('parameter.update', $p->id_param) }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-header bg-success text-white rounded-top-4">
                                <h5 class="modal-title fw-semibold"><i class="bi bi-pencil-square me-2"></i>Edit Parameter</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                    <label class="form-label">Key Parameter</label>
                                    <input type="text" name="key" class="form-control" value="{{ $p->key }}" required>
                                    </div>

                                    <div class="col-md-6">
                                    <label class="form-label">Nama Parameter</label>
                                    <input type="text" name="nama_param" class="form-control" value="{{ $p->nama_param }}" required>
                                    </div>

                                    <div class="col-md-6">
                                    <label class="form-label">Nilai</label>
                                    <input type="number" step="0.01" name="nilai" class="form-control" value="{{ $p->nilai }}" required>
                                    </div>

                                    <div class="col-md-6">
                                    <label class="form-label">Satuan (opsional)</label>
                                    <input type="text" name="satuan" class="form-control" value="{{ $p->satuan }}">
                                    </div>

                                    <div class="col-12">
                                    <label class="form-label">Keterangan (opsional)</label>
                                    <textarea name="keterangan" class="form-control" rows="2">{{ $p->keterangan }}</textarea>
                                    </div>
                                </div>
                                </div>

                                <div class="modal-footer border-0">
                                <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                                </div>

                            </form>
                            </div>
                        </div>
                        </div>


                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4">Belum ada parameter.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('parameter.store') }}">
                @csrf
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Parameter
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Key Parameter</label>
                            <input type="text" name="key" class="form-control" placeholder="contoh: tarif_lembur_per_jam" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nama Parameter</label>
                            <input type="text" name="nama_param" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Nilai</label>
                            <input type="number" name="nilai" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Satuan (opsional)</label>
                            <input type="text" name="satuan" class="form-control" placeholder="contoh: Rupiah / Jam">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Keterangan (opsional)</label>
                            <textarea name="keterangan" class="form-control" rows="2"></textarea>
                        </div>

                    </div>
                </div>

                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">Simpan</button>
                </div>

            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function () {

            const id = this.dataset.id;
            const nama = this.dataset.nama ?? 'data ini';
            const formId = `delete-form-${id}`;

            Swal.fire({
                title: 'Hapus Data?',
                html: `<small class="text-muted">Anda akan menghapus: <b>${nama}</b></small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                customClass: {
                    popup: 'rounded-4',
                    confirmButton: 'rounded-pill px-4',
                    cancelButton: 'rounded-pill px-4'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });

        });
    });

});
</script>
@endsection
