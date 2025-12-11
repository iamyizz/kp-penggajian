@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-0">Kelola Absensi Harian</h2>
        </div>
        <div>
            <a href="{{ route('absensi.rekap') }}" class="btn btn-outline-primary btn-sm me-2">Rekap Bulanan</a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahDataModal">Tambah Data</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">{{ session('warning') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Hadir Hari Ini</h6>
                    <h2 class="mb-0">{{ $todayAttendances->where('status_kehadiran', 'Hadir')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Izin</h6>
                    <h2 class="mb-0">{{ $todayAttendances->where('status_kehadiran', 'Izin')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Sakit</h6>
                    <h2 class="mb-0">{{ $todayAttendances->where('status_kehadiran', 'Sakit')->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="card-title text-muted">Belum Absen</h6>
                    <h2 class="mb-0">{{ $karyawans->count() - $todayAttendances->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Form Row -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('absensi.index') }}">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label">Cari Karyawan</label>
                            <select class="form-select" id="searchKaryawan" name="karyawan_id">
                                <option value="">-- Pilih Karyawan --</option>
                                @foreach($karyawans as $k)
                                    <option value="{{ $k->id_karyawan }}" {{ request('karyawan_id') == $k->id_karyawan ? 'selected' : '' }}>{{ $k->nip }} - {{ $k->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Filter Tanggal</label>
                            <input type="date" class="form-control" id="filterDate" name="date" value="{{ request('date', \Carbon\Carbon::now('Asia/Jakarta')->toDateString()) }}" />
                        </div>
                        <div class="col-md-2 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary" id="searchBtn">Cari</button>
                            <button type="button" class="btn btn-secondary" onclick="window.location='{{ route('absensi.index') }}'"> Reset</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>
        <!-- Search Results Display (server-side) -->
        @if(isset($searchResults) && request()->has('date'))
            <div class="card mb-4" id="searchResults">
                <div class="card-header bg-info text-white">Hasil Pencarian ({{ request('date') }})</div>
                <div class="card-body p-0">
                    <div class="px-3 py-2" id="searchResultsText"></div>
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>NIP</th>
                                    <th>Nama</th>
                                    <th>Tanggal</th>
                                    <th>Jam Masuk</th>
                                    <th>Jam Pulang</th>
                                    <th>Telat</th>
                                    <th>Lembur (jam)</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($searchResults as $a)
                                    <tr>
                                        <td>{{ $a->karyawan->nip ?? '-' }}</td>
                                        <td>{{ $a->karyawan->nama ?? '-' }}</td>
                                        <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                                        <td>{{ substr($a->jam_masuk ?? '-', 0, 5) }}</td>
                                        <td>{{ $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : '-' }}</td>
                                        <td>{{ $a->terlambat ? $a->terlambat : '-' }}</td>
                                        <td>{{ $a->lembur_jam ?? 0 }}</td>
                                        <td><span class="badge bg-success">{{ $a->status_kehadiran }}</span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                            @if(auth()->check() && in_array(auth()->user()->role, ['admin','koor_absen']))
                                                <form action="{{ route('absensi.destroy', $a->id_kehadiran) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center py-4"><small class="text-muted">Tidak ada hasil untuk tanggal ini.</small></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

    <!-- Attendance Table -->
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <div>
                <strong>Daftar Absensi Hari Ini</strong>
                <br>
                <small class="text-muted">
                    Tanggal: <span id="current-date">{{ \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y') }}</span> | 
                    Waktu Indonesia (WIB): <span id="current-time" data-work-start="{{ $workStart }}" data-late-threshold="{{ $lateThreshold }}" class="fw-bold"></span>
                </small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Telat</th>
                            <th>Lembur (jam)</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($todayAttendances as $a)
                            <tr>
                                <td>{{ $a->karyawan->nip ?? '-' }}</td>
                                <td>{{ $a->karyawan->nama ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($a->tanggal)->format('d M Y') }}</td>
                                <td>{{ substr($a->jam_masuk ?? '-', 0, 5) }}</td>
                                <td>{{ $a->jam_keluar ? substr($a->jam_keluar, 0, 5) : '-' }}</td>
                                <td>{{ $a->terlambat ? $a->terlambat : '-' }}</td>
                                <td>{{ $a->lembur_jam ?? 0 }}</td>
                                <td><span class="badge bg-success">{{ $a->status_kehadiran }}</span></td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-outline-primary me-1">Edit</a>
                                    @if(auth()->check() && in_array(auth()->user()->role, ['admin','koor_absen']))
                                        <form action="{{ route('absensi.destroy', $a->id_kehadiran) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center py-4"><small class="text-muted">Belum ada data absensi.</small></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Note/Hint -->
    <div class="mt-3">
        <small class="text-muted">
            <strong>Catatan:</strong> Panel ini hanya untuk pengelolaan data. Proses check-in karyawan tidak dilakukan di sini.
        </small>
    </div>
</div>

<!-- Modal Tambah Data (Optional - untuk Add Data button) -->
<div class="modal fade" id="tambahDataModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Data Absensi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('absensi.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pilih Karyawan</label>
                        <select name="karyawan_id" class="form-select" required>
                            <option value="">-- Pilih --</option>
                            @foreach($karyawans as $k)
                                <option value="{{ $k->id_karyawan }}">{{ $k->nip }} - {{ $k->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Aksi</label>
                        <select name="action" class="form-select" required>
                            <option value="checkin">Ceklok Datang (Check-in)</option>
                            <option value="checkout">Ceklok Pulang (Check-out)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    // Real-time clock for the absensi page (showing Asia/Jakarta time)
    function updateClock(){
        const el = document.getElementById('current-time');
        if(!el) return;

        const fmt = new Intl.DateTimeFormat('en-GB', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false, timeZone: 'Asia/Jakarta' });
        const parts = fmt.formatToParts(new Date());
        const hour = parseInt(parts.find(p => p.type === 'hour').value, 10);
        const minute = parseInt(parts.find(p => p.type === 'minute').value, 10);
        const second = parseInt(parts.find(p => p.type === 'second').value, 10);
        const hh = hour.toString().padStart(2,'0');
        const mm = minute.toString().padStart(2,'0');
        const ss = second.toString().padStart(2,'0');
        el.textContent = `${hh}:${mm}:${ss}`;

        // Visual hint: mark if current Jakarta time is past late threshold
        const workStart = el.dataset.workStart || '{{ $workStart }}';
        const lateThreshold = parseInt(el.dataset.lateThreshold || '{{ $lateThreshold }}', 10);
        const [wsH, wsM] = (workStart || '08:00:00').split(':').map(n => parseInt(n,10));

        const nowMinutes = hour * 60 + minute;
        const thresholdMinutes = (wsH * 60 + wsM) + lateThreshold;

        if(nowMinutes > thresholdMinutes){
            el.classList.add('text-danger');
            el.title = 'Waktu melewati batas terlambat (cek-in setelah ini akan dianggap terlambat)';
        } else {
            el.classList.remove('text-danger');
            el.title = '';
        }
    }
    updateClock();
    setInterval(updateClock, 1000);

    // Filter/Search functionality
    function filterTable() {
        const rawSearchVal = document.getElementById('searchKaryawan').value.trim();
        const searchVal = rawSearchVal.toLowerCase();
        const dateVal = document.getElementById('filterDate').value;
        const tableRows = document.querySelectorAll('table tbody tr');
        let visibleCount = 0;

        tableRows.forEach(row => {
            // Skip empty row
            if(row.textContent.includes('Belum ada data')) {
                row.style.display = '';
                return;
            }

            const cells = row.querySelectorAll('td');
            if(cells.length < 9) return;

            const nip = cells[0]?.textContent.toLowerCase().trim() || '';
            const nama = cells[1]?.textContent.toLowerCase().trim() || '';
            const tanggal = cells[2]?.textContent.trim() || '';
            const statusBadge = cells[7]?.textContent.toLowerCase().trim() || '';

            let matchSearch = true;
            let matchDate = true;

            // Search filter (NIP or Nama) - only if search has value
            if(searchVal) {
                // extract NIP and name by splitting at the first hyphen only
                let sepIndex = rawSearchVal.indexOf('-');
                let selNip = '';
                let selName = '';
                if (sepIndex > -1) {
                    selNip = rawSearchVal.substring(0, sepIndex).toLowerCase().trim();
                    selName = rawSearchVal.substring(sepIndex + 1).toLowerCase().trim();
                } else {
                    selNip = rawSearchVal.toLowerCase();
                }
                matchSearch = (selNip && nip.includes(selNip)) || (selName && nama.includes(selName)) || nama.includes(searchVal) || nip.includes(searchVal);
            }

            // Date filter - only if date is selected
            if(dateVal) {
                const parts = dateVal.split('-');
                const searchDate = parts[2] + ' '; // day
                matchDate = tanggal.includes(searchDate);
            }

            // Show row only if all filters match
            const shouldShow = (matchSearch && matchDate);
            row.style.display = shouldShow ? '' : 'none';
            if(shouldShow) visibleCount++;
        });

        // Show search results message
        const resultsDiv = document.getElementById('searchResults');
        const resultsText = document.getElementById('searchResultsText');

        let filterInfo = [];
        if(searchVal) {
            const sel = document.getElementById('searchKaryawan');
            const selText = sel ? (sel.options[sel.selectedIndex]?.text || rawSearchVal) : rawSearchVal;
            filterInfo.push(`Karyawan: <strong>${selText}</strong>`);
        }
        if(dateVal) filterInfo.push(`Tanggal: <strong>${dateVal}</strong>`);

        if(searchVal || dateVal) {
            if(resultsText) resultsText.innerHTML = `Ditemukan <strong>${visibleCount}</strong> hasil | ${filterInfo.join(' | ')}`;
            if(resultsDiv) resultsDiv.classList.remove('d-none');
        } else {
            if(resultsDiv) resultsDiv.classList.add('d-none');
        }
    }

    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        const searchSelect = document.getElementById('searchKaryawan');
        const dateInput = document.getElementById('filterDate');
        const searchBtn = document.getElementById('searchBtn');
        const resetBtn = document.getElementById('resetBtn');
        const baseUrl = "{{ route('absensi.index') }}";

        // If the page was loaded with a date param (server-side search), enable reset
        @if(request()->has('date') && request('date') != '')
            if(resetBtn) resetBtn.disabled = false;
        @endif

        if(searchBtn) {
            searchBtn.addEventListener('click', function() {
                // allow form submission to occur, but enable reset so user can clear afterwards
                if(resetBtn) resetBtn.disabled = false;
                filterTable();
            });
        }

        // Allow Enter key to trigger search
        if(searchSelect) {
            searchSelect.addEventListener('keypress', function(e) {
                if(e.key === 'Enter') {
                    if(resetBtn) resetBtn.disabled = false;
                    filterTable();
                }
            });
        }

        // Auto-filter on date change
        if(dateInput) {
            dateInput.addEventListener('change', function() {
                if(resetBtn) resetBtn.disabled = false;
                filterTable();
            });
        }

        // Reset button: clear search results and filters
        if(resetBtn) {
            resetBtn.addEventListener('click', function() {
                const resultsDiv = document.getElementById('searchResults');
                const resultsText = document.getElementById('searchResultsText');

                if(resultsDiv) {
                    // Server-side results present: clear card content, hide card, clear inputs and remove query params from URL without reload
                    if(searchSelect) searchSelect.value = '';
                    if(dateInput) dateInput.value = '';
                    try { resultsDiv.innerHTML = ''; } catch(e) { resultsDiv.style.display = 'none'; }
                    if(resultsText) resultsText.innerHTML = '';
                    try { history.replaceState(null, '', baseUrl); } catch(e) { /* ignore */ }
                    if(resetBtn) resetBtn.disabled = true;
                } else {
                    // No server-side results -> perform client-side reset
                    window.resetFilters();
                }
            });
        }

        // Expose a simple reset function (clears filters client-side without navigation)
        window.resetFilters = function() {
            if(searchSelect) searchSelect.value = '';
            if(dateInput) dateInput.value = '';
            filterTable();
            if(resetBtn) resetBtn.disabled = true;
        };
    });
</script>
@endpush
