<?php

namespace App\Http\Controllers;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

        // use Indonesia timezone (WIB) for date calculations
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $attendances = Kehadiran::with('karyawan')
            ->where('tanggal', $today)
            ->orderBy('jam_masuk')
            ->get();

        return view('absensi.index', compact('karyawans', 'attendances', 'workStart', 'workEnd', 'lateThreshold'));
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
                $jamMasuk = Carbon::createFromFormat('H:i:s', $r->jam_masuk, 'Asia/Jakarta');
                $scheduledStart = Carbon::createFromFormat('H:i:s', $workStart->format('H:i:s'), 'Asia/Jakarta');
                if ($jamMasuk->greaterThan($scheduledStart)) {
                    $summary['terlambat_minutes'] += $jamMasuk->diffInMinutes($scheduledStart);
                }
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
}
