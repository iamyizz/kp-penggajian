<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use App\Models\TunjanganKehadiranMakan;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil data penggajian per periode (grouped by bulan & tahun)
        $data = Penggajian::query()
            ->selectRaw('
                periode_tahun,
                periode_bulan,
                COUNT(DISTINCT karyawan_id) as total_karyawan,
                COALESCE(SUM(lembur), 0) as total_lembur,
                COALESCE(SUM(bonus), 0) as total_bonus,
                COALESCE(SUM(potongan_absen), 0) as total_potongan_absen,
                COALESCE(SUM(potongan_bpjs), 0) as total_potongan_bpjs,
                COALESCE(SUM(potongan_absen + potongan_bpjs), 0) as total_potongan,
                COALESCE(SUM(total_gaji), 0) as total_biaya_gaji,
                MAX(is_approved) as is_approved
            ')
            ->groupBy('periode_tahun', 'periode_bulan')
            ->orderByDesc('periode_tahun')
            ->orderByDesc('periode_bulan')
            ->get();

        // Dashboard Mini: Menghitung total keseluruhan
        $totalKaryawan = Karyawan::count();
        $totalLembur = Penggajian::sum('lembur');
        $totalPotongan = Penggajian::sum('potongan_absen') + Penggajian::sum('potongan_bpjs');
        $totalBiayaGaji = Penggajian::sum('total_gaji');

        return view('laporan.index', compact(
            'totalKaryawan', 'totalLembur', 'totalPotongan', 'totalBiayaGaji', 'data'
        ));
    }

    public function periodeDetail($tahun, $bulan)
    {
        $tahun = (int) $tahun;
        $bulan = (int) $bulan;

        // Ambil semua data penggajian pada periode tersebut
        $data = Penggajian::with(['karyawan.jabatan'])
            ->where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->orderBy('karyawan_id')
            ->get();

        // Jika tidak ada data, redirect kembali
        if ($data->isEmpty()) {
            return redirect()->route('laporan.index')
                ->with('error', 'Data penggajian untuk periode tersebut tidak ditemukan.');
        }

        // Hitung ringkasan periode
        $ringkasan = [
            'total_karyawan'    => $data->count(),
            'total_gaji_pokok'  => $data->sum('gaji_pokok'),
            'total_tunjangan'   => $data->sum('tunjangan_jabatan') + $data->sum('tunjangan_kehadiran_makan'),
            'total_lembur'      => $data->sum('lembur'),
            'total_bonus'       => $data->sum('bonus'),
            'total_potongan'    => $data->sum('potongan_absen') + $data->sum('potongan_bpjs'),
            'total_biaya_gaji'  => $data->sum('total_gaji'),
            'is_approved'       => $data->every(fn($item) => $item->is_approved),
        ];

        // Nama bulan dalam Bahasa Indonesia
        $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');

        return view('laporan.periode_detail', compact(
            'data', 'ringkasan', 'tahun', 'bulan', 'namaBulan'
        ));
    }

    // Menampilkan halaman approve periode penggajian untuk owner
    public function approvePage()
    {
        // Ambil semua periode penggajian yang ada (group by tahun & bulan)
        $periodeList = Penggajian::select(
                'periode_tahun',
                'periode_bulan',
                DB::raw('COUNT(*) as total_karyawan'),
                DB::raw('SUM(total_gaji) as total_biaya_gaji'),
                DB::raw('SUM(gaji_pokok) as total_gaji_pokok'),
                DB::raw('SUM(tunjangan_jabatan + tunjangan_kehadiran_makan) as total_tunjangan'),
                DB::raw('SUM(lembur) as total_lembur'),
                DB::raw('SUM(bonus) as total_bonus'),
                DB::raw('SUM(potongan_absen + potongan_bpjs) as total_potongan'),
                DB::raw('MAX(is_approved) as is_approved'),
                DB::raw('MAX(tanggal_proses) as tanggal_proses')
            )
            ->groupBy('periode_tahun', 'periode_bulan')
            ->orderByDesc('periode_tahun')
            ->orderByDesc('periode_bulan')
            ->get()
            ->map(function ($item) {
                $item->nama_bulan = Carbon::create()->month($item->periode_bulan)->translatedFormat('F');
                return $item;
            });

        // Pisahkan pending dan approved
        $pendingApproval = $periodeList->where('is_approved', false);
        $approvedList = $periodeList->where('is_approved', true);

        return view('laporan.approve', compact('pendingApproval', 'approvedList'));
    }

    /**
     * Proses Approve Periode Penggajian
     */
    public function approvePeriode(Request $request, $tahun, $bulan)
    {
        // ✅ Tambahkan casting ini
        $tahun = (int) $tahun;
        $bulan = (int) $bulan;

        // Validasi periode exists
        $exists = Penggajian::where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->exists();

        if (!$exists) {
            return back()->with('error', 'Periode penggajian tidak ditemukan.');
        }

        // Cek apakah sudah di-approve
        $alreadyApproved = Penggajian::where('periode_tahun', $tahun)
            ->where('periode_bulan', $bulan)
            ->where('is_approved', true)
            ->exists();

        if ($alreadyApproved) {
            return back()->with('warning', 'Periode ini sudah di-approve sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Update semua penggajian di periode ini
            Penggajian::where('periode_tahun', $tahun)
                ->where('periode_bulan', $bulan)
                ->update([
                    'is_approved' => true,
                    'tanggal_proses' => now(),
                ]);

            DB::commit();

            $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
            return back()->with('success', "Periode {$namaBulan} {$tahun} berhasil di-approve!");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    /**
     * Proses Reject/Batalkan Approve Periode (Opsional)
     */
    public function rejectPeriode(Request $request, $tahun, $bulan)
    {
        // ✅ Tambahkan casting ini
        $tahun = (int) $tahun;
        $bulan = (int) $bulan;

        $request->validate([
            'alasan' => 'nullable|string|max:500'
        ]);

        try {
            DB::beginTransaction();

            // ✅ Perbaiki juga nama kolom: tahun → periode_tahun, bulan → periode_bulan
            Penggajian::where('periode_tahun', $tahun)
                ->where('periode_bulan', $bulan)
                ->update([
                    'is_approved' => false,
                    'tanggal_proses' => now(),
                    // Hapus kolom yang tidak ada di tabel
                    // 'approved_at' => null,
                    // 'approved_by' => null,
                    // 'rejected_reason' => $request->alasan,
                ]);

            DB::commit();

            $namaBulan = Carbon::create()->month($bulan)->translatedFormat('F');
            return back()->with('success', "Approval periode {$namaBulan} {$tahun} berhasil dibatalkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function slipPdf($id)
    {
        $gaji = Penggajian::with('karyawan')->findOrFail($id);

        // Ambil absensi detail
        $absensi = Kehadiran::where('karyawan_id', $gaji->karyawan_id)
            ->whereMonth('tanggal', $gaji->periode_bulan)
            ->whereYear('tanggal', $gaji->periode_tahun)
            ->get();

        $dataAbsensi = [
            'hadir'      => $absensi->where('status_kehadiran', 'Hadir')->count(),
            'izin'       => $absensi->where('status_kehadiran', 'Izin')->count(),
            'sakit'      => $absensi->where('status_kehadiran', 'Sakit')->count(),
            'alpa'       => $absensi->where('status_kehadiran', 'Alpa')->count(),
            'terlambat'  => $absensi->where('terlambat', true)->count(),
            'lembur_jam' => $absensi->sum('lembur_jam'),
        ];

        $pdf = PDF::loadView('laporan.slip_pdf', [
            'gaji' => $gaji,
            'absensi' => $dataAbsensi,
        ])->setPaper('A5', 'portrait');

        return $pdf->download('Slip-Gaji-'.$gaji->karyawan->nama.'.pdf');
    }
}
