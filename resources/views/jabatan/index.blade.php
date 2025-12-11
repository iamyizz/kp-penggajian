@extends('layouts.app')

@section('content')
<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">🧩 Data Jabatan</h2>
            <p class="text-muted small mb-0">Mengatur daftar jabatan dan komponen gaji pokok & tunjangan</p>
        </div>
        <button class="btn btn-success shadow-sm px-3 rounded-pill"
                data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-plus-circle me-1"></i> Tambah Jabatan
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
                        <th width="5%">#</th>
                        <th>Nama Jabatan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan Jabatan</th>
                        <th width="18%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($jabatan as $j)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="fw-semibold">{{ $j->nama_jabatan }}</td>
                            <td>Rp {{ number_format($j->gaji_pokok, 0, ',', '.') }}</td>
                            <td>Rp {{ number_format($j->tunjangan_jabatan, 0, ',', '.') }}</td>

                            <td>
                                <div class="d-flex justify-content-center gap-2">

                                    {{-- Edit --}}
                                    <button class="btn btn-sm btn-outline-warning px-2"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit{{ $j->id_jabatan }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger px-2 btn-delete"
                                            data-id="{{ $j->id_jabatan }}"
                                            data-nama="{{ $j->nama_jabatan }}"
                                            data-url="{{ route('jabatan.destroy', $j->id_jabatan) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $j->id_jabatan }}"
                                        action="{{ route('jabatan.destroy', $j->id_jabatan) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit{{ $j->id_jabatan }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                            <form method="POST" action="{{ route('jabatan.update', $j->id_jabatan) }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-header bg-warning text-white rounded-top-4">
                                    <h5 class="modal-title fw-semibold">
                                        <i class="bi bi-pencil-square me-2"></i>Edit Jabatan
                                    </h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">
                                    <div class="row g-3">

                                        <div class="col-md-12">
                                            <label class="form-label">Nama Jabatan</label>
                                            <input type="text" name="nama_jabatan" class="form-control"
                                                value="{{ $j->nama_jabatan }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Gaji Pokok</label>
                                            <input type="number" name="gaji_pokok" class="form-control"
                                                value="{{ $j->gaji_pokok }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Tunjangan Jabatan</label>
                                            <input type="number" name="tunjangan_jabatan" class="form-control"
                                                value="{{ $j->tunjangan_jabatan }}" required>
                                        </div>

                                    </div>
                                </div>

                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-warning rounded-pill px-4 text-white">Simpan</button>
                                </div>

                            </form>
                            </div>
                        </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="6" class="text-muted py-4">Belum ada data jabatan.</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah --}}
<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('jabatan.store') }}">
                @csrf
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Jabatan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <div class="col-md-12">
                            <label class="form-label">Nama Jabatan</label>
                            <input type="text" name="nama_jabatan" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok</label>
                            <input type="number" name="gaji_pokok" class="form-control" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Tunjangan Jabatan</label>
                            <input type="number" name="tunjangan_jabatan" class="form-control" required>
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
