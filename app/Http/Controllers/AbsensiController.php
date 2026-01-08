<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AbsensiImport;

class AbsensiController extends Controller
{
    /**
     * Display form and today's attendance list / recap options.
     */
    public function index(Request $request)
    {
        $workStart = config('attendance.work_start', '08:00:00');
        $workEnd = config('attendance.work_end', '16:00:00');
        $lateThreshold = (int) config('attendance.late_threshold_minutes', 5);

        $karyawans = Karyawan::where('aktif', true)->orderBy('nama')->get();
        // prepare today's attendances (always show)
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $todayAttendances = Kehadiran::with('karyawan')
            ->where('tanggal', $today)
            ->orderBy('jam_masuk')
            ->get();

        // Accept optional filters from request: date and karyawan_id
        $date = $request->input('date');
        $karyawanId = $request->input('karyawan_id');

        $searchResults = collect();
        if ($date) {
            $query = Kehadiran::with('karyawan')->where('tanggal', $date)->orderBy('jam_masuk');
            if ($karyawanId) {
                $query->where('karyawan_id', $karyawanId);
            }
            $searchResults = $query->get();
        }

        // Accept optional rekap filters: karyawan_id (for rekap), month, year
        $rekapKaryawanId = $request->input('rekap_karyawan_id');
        $month = $request->input('month', Carbon::now('Asia/Jakarta')->month);
        $year = $request->input('year', Carbon::now('Asia/Jakarta')->year);

        $karyawan = null;
        $rekapRecords = collect();
        $rekapSummary = null;

        if ($rekapKaryawanId) {
            $karyawan = Karyawan::find($rekapKaryawanId);
            if ($karyawan) {
                $rekapRecords = Kehadiran::where('karyawan_id', $rekapKaryawanId)
                    ->whereYear('tanggal', $year)
                    ->whereMonth('tanggal', $month)
                    ->orderBy('tanggal')
                    ->get();

                $rekapSummary = [
                    'hadir' => 0,
                    'izin' => 0,
                    'sakit' => 0,
                    'alpha' => 0,
                    'terlambat_count' => 0,
                    'terlambat_minutes' => 0,
                    'pulang_cepat' => 0,
                    'lembur_jam_total' => 0,
                ];

                foreach ($rekapRecords as $r) {
                    switch ($r->status_kehadiran) {
                        case 'Hadir': $rekapSummary['hadir']++; break;
                        case 'Izin': $rekapSummary['izin']++; break;
                        case 'Sakit': $rekapSummary['sakit']++; break;
                        case 'Alpa': $rekapSummary['alpha']++; break;
                    }

                    if ($r->terlambat) {
                        $rekapSummary['terlambat_count']++;
                    }

                    if ($r->jam_masuk) {
                        $tanggal = Carbon::parse($r->tanggal, 'Asia/Jakarta');

                        $jamMasuk = Carbon::parse(
                            $tanggal->format('Y-m-d') . ' ' . $r->jam_masuk,
                            'Asia/Jakarta'
                        );

                        $scheduledStart = Carbon::parse(
                            $tanggal->format('Y-m-d') . ' ' . config('attendance.work_start', '08:00:00'),
                            'Asia/Jakarta'
                        );

                        // hitung selisih (boleh minus)
                        $selisih = $scheduledStart->diffInMinutes($jamMasuk, false);

                        // hanya tambahkan jika benar-benar terlambat
                        $rekapSummary['terlambat_minutes'] += max(0, $selisih);
                    }


                    if ($r->jam_keluar) {
                        $jamKeluar = Carbon::createFromFormat('H:i:s', $r->jam_keluar, 'Asia/Jakarta');
                        $scheduledEnd = Carbon::createFromFormat('H:i:s', $workEnd, 'Asia/Jakarta');
                        if ($jamKeluar->lessThan($scheduledEnd)) {
                            $rekapSummary['pulang_cepat']++;
                        }
                    }

                    // Add overtime (lembur) hours
                    if ($r->lembur_jam) {
                        $rekapSummary['lembur_jam_total'] += $r->lembur_jam;
                    }
                }
            }
        }

        return view('absensi.index', compact('karyawans', 'todayAttendances', 'searchResults', 'workStart', 'workEnd', 'lateThreshold', 'karyawan', 'rekapRecords', 'rekapSummary', 'month', 'year'));
    }

    /**
     * Handle check-in / check-out actions.
     */
    public function store(Request $request)
    {
        $request->validate([
            'karyawan_id' => 'required|exists:karyawan,id_karyawan',
            'action' => 'required|in:checkin,checkout',
        ]);

        $karyawanId = $request->input('karyawan_id');
        $action = $request->input('action');

        // server time should follow Indonesian timezone
        $now = Carbon::now('Asia/Jakarta');
        $today = $now->toDateString();

        $workStart = Carbon::createFromFormat('H:i:s', config('attendance.work_start', '08:00:00'), 'Asia/Jakarta');
        $workEnd = Carbon::createFromFormat('H:i:s', config('attendance.work_end', '16:00:00'), 'Asia/Jakarta');
        $lateThreshold = (int) config('attendance.late_threshold_minutes', 5);

        if ($action === 'checkin') {
            // if already exists today's attendance, don't duplicate
            $existing = Kehadiran::where('karyawan_id', $karyawanId)->where('tanggal', $today)->first();
            if ($existing) {
                return back()->with('warning', 'Sudah melakukan check-in hari ini untuk karyawan tersebut.');
            }

            $jamMasuk = $now->format('H:i:s');
            // determine terlambat: on-time only if jam_masuk is within [workStart, workStart+lateThreshold]
            $scheduledStart = Carbon::createFromFormat('Y-m-d H:i:s', $now->toDateString().' '.$workStart->format('H:i:s'), 'Asia/Jakarta');
            // $scheduledEnd = $scheduledStart->copy()->addMinutes($lateThreshold);
            // sebelumnya menggunakan between untuk cek terlambat, sekarang ganti dengan pengecekan eksplisit
            $lateLimit = $scheduledStart->copy()->addMinutes($lateThreshold);
            $isLate = $now->gt($lateLimit);

            Kehadiran::create([
                'karyawan_id' => $karyawanId,
                'tanggal' => $today,
                'status_kehadiran' => 'Hadir',
                'jam_masuk' => $jamMasuk,
                'terlambat' => $isLate,
                'lembur_jam' => 0,
            ]);

            return back()->with('success', 'Check-in berhasil.');
        }

        if ($action === 'checkout') {
            $attendance = Kehadiran::where('karyawan_id', $karyawanId)->where('tanggal', $today)->first();
            if (! $attendance) {
                return back()->with('warning', 'Belum ada record check-in untuk hari ini.');
            }

            if ($attendance->jam_keluar) {
                return back()->with('warning', 'Sudah melakukan check-out hari ini.');
            }

            $jamKeluar = $now->format('H:i:s');

            // calculate lembur (hours) if jam_keluar > workEnd
            $workEndToday = Carbon::createFromFormat('Y-m-d H:i:s', $today.' '.$workEnd->format('H:i:s'), 'Asia/Jakarta');
            $lemburHours = 0;
            if ($now->greaterThan($workEndToday)) {
                $lemburMinutes = $now->diffInMinutes($workEndToday);
                $lemburHours = round($lemburMinutes / 60, 2);
            }

            // determine if pulang cepat
            $pulangCepat = $now->lessThan($workEndToday);

            $attendance->update([
                'jam_keluar' => $jamKeluar,
                'lembur_jam' => $lemburHours,
            ]);

            // we don't have a column for pulang cepat; can be computed in rekap

            return back()->with('success', 'Check-out berhasil.');
        }

        return back()->with('error', 'Aksi tidak dikenali.');
    }

    /**
     * Rekap absensi bulanan untuk karyawan tertentu.
     */
    public function rekap(Request $request)
    {
        // allow showing selection form if karyawan_id not provided
        $request->validate([
            'karyawan_id' => 'nullable|exists:karyawan,id_karyawan',
            'month' => 'nullable|integer|min:1|max:12',
            'year' => 'nullable|integer|min:2000',
        ]);

        $karyawanId = $request->input('karyawan_id');
        if (! $karyawanId) {
            // show selection form
            $karyawans = Karyawan::where('aktif', true)->orderBy('nama')->get();
            $month = $request->input('month', Carbon::now('Asia/Jakarta')->month);
            $year = $request->input('year', Carbon::now('Asia/Jakarta')->year);
            return view('absensi.rekap_select', compact('karyawans', 'month', 'year'));
        }
        $month = $request->input('month', Carbon::now('Asia/Jakarta')->month);
        $year = $request->input('year', Carbon::now('Asia/Jakarta')->year);

        $karyawans = Karyawan::where('aktif', true)->orderBy('nama')->get();
        $karyawan = Karyawan::find($karyawanId);
        if (! $karyawan) {
            abort(404, 'Karyawan tidak ditemukan.');
        }

        $workStart = Carbon::createFromFormat('H:i:s', config('attendance.work_start', '08:00:00'), 'Asia/Jakarta');
        $workEnd = Carbon::createFromFormat('H:i:s', config('attendance.work_end', '16:00:00'), 'Asia/Jakarta');
        $lateThreshold = (int) config('attendance.late_threshold_minutes', 5);

        $records = Kehadiran::where('karyawan_id', $karyawanId)
            ->whereYear('tanggal', $year)
            ->whereMonth('tanggal', $month)
            ->orderBy('tanggal')
            ->get();

        $summary = [
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
            'terlambat_count' => 0,
            'terlambat_minutes' => 0,
            'pulang_cepat' => 0,
        ];

        foreach ($records as $r) {
            switch ($r->status_kehadiran) {
                case 'Hadir': $summary['hadir']++; break;
                case 'Izin': $summary['izin']++; break;
                case 'Sakit': $summary['sakit']++; break;
                case 'Alpa': $summary['alpha']++; break;
            }

            if ($r->terlambat) {
                $summary['terlambat_count']++;
            }

            if ($r->jam_masuk) {
                $tanggal = Carbon::parse($r->tanggal, 'Asia/Jakarta');

                $jamMasuk = Carbon::parse(
                    $tanggal->format('Y-m-d') . ' ' . $r->jam_masuk,
                    'Asia/Jakarta'
                );

                $scheduledStart = Carbon::parse(
                    $tanggal->format('Y-m-d') . ' ' . config('attendance.work_start', '08:00:00'),
                    'Asia/Jakarta'
                );

                // hitung selisih (positif = terlambat, negatif = lebih awal)
                $selisih = $jamMasuk->diffInMinutes($scheduledStart, false);


            }

            if ($r->jam_keluar) {
                $jamKeluar = Carbon::createFromFormat('H:i:s', $r->jam_keluar, 'Asia/Jakarta');
                $scheduledEnd = Carbon::createFromFormat('H:i:s', $workEnd->format('H:i:s'), 'Asia/Jakarta');
                if ($jamKeluar->lessThan($scheduledEnd)) {
                    $summary['pulang_cepat']++;
                }
            }
        }

        return view('absensi.rekap', compact('karyawans', 'karyawan', 'records', 'summary', 'month', 'year'));
    }

    /**
     * Show the form for editing the specified attendance.
     */
    public function edit(string $id)
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'koor_absen'])) {
            abort(403, 'Unauthorized action.');
        }

        $attendance = Kehadiran::findOrFail($id);
        $karyawans = Karyawan::where('aktif', true)->orderBy('nama')->get();

        return view('absensi.edit', compact('attendance', 'karyawans'));
    }

    /**
     * Update the specified attendance in storage.
     */
    public function update(Request $request, string $id)
    {
        $user = auth()->user();
        if (! $user || ! in_array($user->role, ['admin', 'koor_absen'])) {
            abort(403, 'Unauthorized action.');
        }

        $attendance = Kehadiran::findOrFail($id);

        $data = $request->validate([
            'status_kehadiran' => 'required|string',
            'jam_masuk' => 'nullable|string',
            'jam_keluar' => 'nullable|string',
            'lembur_jam' => 'nullable|numeric',
        ]);

        // Normalize times to H:i:s when provided
        try {
            if (! empty($data['jam_masuk'])) {
                $jamMasuk = Carbon::parse($data['jam_masuk'])->format('H:i:s');
                $attendance->jam_masuk = $jamMasuk;
            } else {
                $attendance->jam_masuk = null;
            }
        } catch (\Exception $e) {
            // ignore parse error, keep original value
        }

        try {
            if (! empty($data['jam_keluar'])) {
                $jamKeluar = Carbon::parse($data['jam_keluar'])->format('H:i:s');
                $attendance->jam_keluar = $jamKeluar;
            } else {
                $attendance->jam_keluar = null;
            }
        } catch (\Exception $e) {
            // ignore parse error
        }

        // Update status
        $attendance->status_kehadiran = $data['status_kehadiran'];

        // Recalculate terlambat based on configured work start + threshold
        $workStart = Carbon::createFromFormat('H:i:s', config('attendance.work_start', '08:00:00'), 'Asia/Jakarta');
        $lateThreshold = (int) config('attendance.late_threshold_minutes', 5);
        if ($attendance->jam_masuk) {
            $jamMasukTime = Carbon::createFromFormat('H:i:s', $attendance->jam_masuk, 'Asia/Jakarta');
            $attendance->terlambat = $jamMasukTime->gt($workStart->copy()->addMinutes($lateThreshold));
        } else {
            $attendance->terlambat = false;
        }

        // Recalculate lembur_jam if jam_keluar is present and greater than work end
        if ($attendance->jam_keluar) {
            $workEnd = Carbon::createFromFormat('H:i:s', config('attendance.work_end', '16:00:00'), 'Asia/Jakarta');
            $jamKeluarTime = Carbon::createFromFormat('H:i:s', $attendance->jam_keluar, 'Asia/Jakarta');
            if ($jamKeluarTime->greaterThan($workEnd)) {
                $minutes = $jamKeluarTime->diffInMinutes($workEnd);
                $attendance->lembur_jam = round($minutes / 60, 2);
            } else {
                // allow manual override via input if provided
                $attendance->lembur_jam = isset($data['lembur_jam']) ? $data['lembur_jam'] : 0;
            }
        } else {
            $attendance->lembur_jam = isset($data['lembur_jam']) ? $data['lembur_jam'] : 0;
        }

        $attendance->save();

        return redirect()->route('absensi.index', [
            'rekap_karyawan_id' => $attendance->karyawan_id,
            'month' => Carbon::parse($attendance->tanggal)->month,
            'year' => Carbon::parse($attendance->tanggal)->year,
        ])->with('success', 'Data absensi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // allow only admin or koor_absen (middleware already restricts but double-check)
        $user = auth()->guard()->user();
        if (! $user || ! in_array($user->role, ['admin', 'koor_absen'])) {
            abort(403, 'Unauthorized action.');
        }

        $attendance = Kehadiran::findOrFail($id);
        $attendance->delete();

        return back()->with('success', 'Data absensi berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);
        try {
            Excel::import(new AbsensiImport, $request->file('file'));
            return back()->with('success', 'Import data absensi berhasil.');
        } catch (\Exception $e) {
            return back()->with('warning', 'Terjadi kesalahan saat mengimport: '.$e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $headers = ['nip', 'tanggal', 'status_kehadiran', 'jam_masuk', 'jam_keluar', 'terlambat', 'lembur_jam'];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, $headers);

            // Contoh data dengan format YYYY-MM-DD
            fputcsv($file, ['KLSM-0001', '2024-06-03', 'Hadir', '08:00:00', '17:00:00', '0', '0.00']);
            fputcsv($file, ['KLSM-0001', '2024-06-04', 'Izin', '', '', '0', '0.00']);

            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_absensi.csv"',
        ]);
    }
}
