@extends('layouts.app')

@section('content')
<div class="container mt-4">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-success mb-0">📋 Data Karyawan</h2>
            <p class="text-muted small mb-0">Kelola informasi seluruh karyawan Klinik Samara</p>
        </div>
        <button class="btn btn-success shadow-sm px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#modalTambah">
            <i class="bi bi-person-plus me-1"></i> Tambah Karyawan
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
                        <th width="10%">NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Status</th>
                        <th>Aktif</th>
                        <th width="20%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $k)
                        <tr>
                            <td class="fw-semibold text-secondary">{{ $k->nip }}</td>
                            <td>{{ $k->nama }}</td>
                            <td>{{ $k->jabatan->nama_jabatan ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill bg-light text-dark border">
                                    {{ $k->status_karyawan }}
                                </span>
                            </td>
                            <td>
                                <span class="badge rounded-pill {{ $k->aktif ? 'bg-success-subtle text-success border border-success' : 'bg-secondary-subtle text-muted border border-secondary' }}">
                                    {{ $k->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-sm btn-outline-warning px-2"
                                            data-bs-toggle="modal" data-bs-target="#modalEdit{{ $k->id_karyawan }}">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger px-2 btn-delete"
                                            data-id="{{ $k->id_karyawan }}"
                                            data-nama="{{ $k->nama }}"
                                            data-url="{{ route('karyawan.destroy', $k->id_karyawan) }}">
                                        <i class="bi bi-trash"></i>
                                    </button>

                                    <form id="delete-form-{{ $k->id_karyawan }}"
                                        action="{{ route('karyawan.destroy', $k->id_karyawan) }}"
                                        method="POST" class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal Edit --}}
                        <div class="modal fade" id="modalEdit{{ $k->id_karyawan }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                <div class="modal-content border-0 shadow-lg rounded-4">
                                    <form method="POST" action="{{ route('karyawan.update', $k->id_karyawan) }}">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header bg-success text-white rounded-top-4">
                                            <h5 class="modal-title fw-semibold">
                                                <i class="bi bi-pencil-square me-2"></i>Edit Karyawan
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">NIP</label>
                                                    <input type="text" name="nip" value="{{ $k->nip }}" class="form-control bg-light" readonly>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Nama</label>
                                                    <input type="text" name="nama" value="{{ $k->nama }}" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Jabatan</label>
                                                    <select name="jabatan_id" class="form-select" required>
                                                        @foreach ($jabatans as $j)
                                                            <option value="{{ $j->id_jabatan }}" {{ $j->id_jabatan == $k->jabatan_id ? 'selected' : '' }}>
                                                                {{ $j->nama_jabatan }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Tanggal Masuk</label>
                                                    <input type="date" name="tanggal_masuk" value="{{ $k->tanggal_masuk }}" class="form-control" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Status Karyawan</label>
                                                    <select name="status_karyawan" class="form-select">
                                                        <option value="Tetap" {{ $k->status_karyawan == 'Tetap' ? 'selected' : '' }}>Tetap</option>
                                                        <option value="Kontrak" {{ $k->status_karyawan == 'Kontrak' ? 'selected' : '' }}>Kontrak</option>
                                                        <option value="Magang" {{ $k->status_karyawan == 'Magang' ? 'selected' : '' }}>Magang</option>
                                                        <option value="Outsource" {{ $k->status_karyawan == 'Outsource' ? 'selected' : '' }}>Outsource</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Rekening Bank</label>
                                                    <select name="rekening_bank" class="form-select">
                                                        <option value="BCA" {{ $k->rekening_bank == 'BCA' ? 'selected' : '' }}>BCA</option>
                                                        <option value="BNI" {{ $k->rekening_bank == 'BNI' ? 'selected' : '' }}>BNI</option>
                                                        <option value="BRI" {{ $k->rekening_bank == 'BRI' ? 'selected' : '' }}>BRI</option>
                                                        <option value="Mandiri" {{ $k->rekening_bank == 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                                                        <option value="CIMB Niaga" {{ $k->rekening_bank == 'CIMB Niaga' ? 'selected' : '' }}>CIMB Niaga</option>
                                                        <option value="Danamon" {{ $k->rekening_bank == 'Danamon' ? 'selected' : '' }}>Danamon</option>
                                                        <option value="BTN" {{ $k->rekening_bank == 'BTN' ? 'selected' : '' }}>BTN</option>
                                                        <option value="Permata" {{ $k->rekening_bank == 'Permata' ? 'selected' : '' }}>Permata</option>
                                                        <option value="Maybank" {{ $k->rekening_bank == 'Maybank' ? 'selected' : '' }}>Maybank</option>
                                                        <option value="OCBC NISP" {{ $k->rekening_bank == 'OCBC NISP' ? 'selected' : '' }}>OCBC NISP</option>
                                                        <option value="Panin" {{ $k->rekening_bank == 'Panin' ? 'selected' : '' }}>Panin</option>
                                                        <option value="Sinarmas" {{ $k->rekening_bank == 'Sinarmas' ? 'selected' : '' }}>Sinarmas</option>
                                                        <option value="Mega" {{ $k->rekening_bank == 'Mega' ? 'selected' : '' }}>Mega</option>
                                                        <option value="BJB" {{ $k->rekening_bank == 'BJB' ? 'selected' : '' }}>BJB</option>
                                                        <option value="BTPN" {{ $k->rekening_bank == 'BTPN' ? 'selected' : '' }}>BTPN</option>
                                                        <option value="Bank Syariah Indonesia" {{ $k->rekening_bank == 'Bank Syariah Indonesia' ? 'selected' : '' }}>BSI</option>
                                                        <option value="Bank Jatim" {{ $k->rekening_bank == 'Bank Jatim' ? 'selected' : '' }}>Bank Jatim</option>
                                                        <option value="Bank Jateng" {{ $k->rekening_bank == 'Bank Jateng' ? 'selected' : '' }}>Bank Jateng</option>
                                                        <option value="Bank Papua" {{ $k->rekening_bank == 'Bank Papua' ? 'selected' : '' }}>Bank Papua</option>
                                                        <option value="Bank Sulselbar" {{ $k->rekening_bank == 'Bank Sulselbar' ? 'selected' : '' }}>Bank Sulselbar</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Aktif</label>
                                                    <select name="aktif" class="form-select">
                                                        <option value="1" {{ $k->aktif ? 'selected' : '' }}>Aktif</option>
                                                        <option value="0" {{ !$k->aktif ? 'selected' : '' }}>Nonaktif</option>
                                                    </select>
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
                            <td colspan="6" class="text-muted py-4">Belum ada data karyawan.</td>
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
            <form method="POST" action="{{ route('karyawan.store') }}">
                @csrf
                <div class="modal-header bg-success text-white rounded-top-4">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-person-plus me-2"></i>Tambah Karyawan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control bg-light" value="{{ $nextNip }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan_id" class="form-select" required>
                                @foreach ($jabatans as $j)
                                    <option value="{{ $j->id_jabatan }}">{{ $j->nama_jabatan }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Karyawan</label>
                            <select name="status_karyawan" class="form-select">
                                <option value="Tetap">Tetap</option>
                                <option value="Kontrak">Kontrak</option>
                                <option value="Magang">Magang</option>
                                <option value="Outsource">Outsource</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Rekening Bank</label>
                            <select name="rekening_bank" class="form-select">
                                <option value="" selected disabled>Pilih Bank</option>
                                <option value="BCA">BCA (Bank Central Asia)</option>
                                <option value="BNI">BNI (Bank Negara Indonesia)</option>
                                <option value="BRI">BRI (Bank Rakyat Indonesia)</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="CIMB Niaga">CIMB Niaga</option>
                                <option value="Danamon">Danamon</option>
                                <option value="BTN">BTN (Bank Tabungan Negara)</option>
                                <option value="Permata">Permata</option>
                                <option value="Maybank">Maybank</option>
                                <option value="OCBC NISP">OCBC NISP</option>
                                <option value="Panin">Panin Bank</option>
                                <option value="Sinarmas">Sinarmas</option>
                                <option value="Mega">Mega</option>
                                <option value="BJB">BJB (Bank Jabar Banten)</option>
                                <option value="BTPN">BTPN</option>
                                <option value="Bank Syariah Indonesia">Bank Syariah Indonesia (BSI)</option>
                                <option value="Bank Jatim">Bank Jatim</option>
                                <option value="Bank Jateng">Bank Jateng</option>
                                <option value="Bank Papua">Bank Papua</option>
                                <option value="Bank Sulselbar">Bank Sulselbar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Aktif</label>
                            <select name="aktif" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Nonaktif</option>
                            </select>
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
