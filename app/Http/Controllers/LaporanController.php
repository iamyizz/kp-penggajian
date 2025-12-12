<?php

namespace App\Http\Controllers;

use App\Models\Penggajian;
use App\Models\Karyawan;
use App\Models\Kehadiran;
use Illuminate\Http\Request;
use App\Models\TunjanganKehadiranMakan;
use Barryvdh\DomPDF\Facade\Pdf as PDF;


class LaporanController extends Controller
{
    public function index(Request $request)
    {
        // optional: filter per bulan
        $bulanTahun = $request->get('bulan');
        if ($bulanTahun) {
            [$tahun, $bulan] = explode('-', $bulanTahun);
        } else {
            $bulan = date('m');
            $tahun = date('Y');
            $bulanTahun = "$tahun-$bulan";
        }


        // Dashboard Mini
        $totalKaryawan = Karyawan::count();

        $totalLembur = Penggajian::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->sum('lembur');

        $totalPotongan = Penggajian::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->get()
            ->sum(fn($x) => ($x->potongan_absen + $x->potongan_bpjs));

        $totalBiayaGaji = Penggajian::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->sum('total_gaji');

        $rataGaji = Penggajian::where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->avg('total_gaji');

        // Tabel Data Gaji
        $data = Penggajian::with('karyawan')
            ->where('periode_bulan', $bulan)
            ->where('periode_tahun', $tahun)
            ->get();

        // Ambil semua tunjangan kehadiran makan bulan ini
        $tunjanganList = TunjanganKehadiranMakan::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get()
            ->keyBy('karyawan_id'); // supaya mudah diakses di view


        $dataAbsensi = [];

        foreach ($data as $gaji) {

            $absensi = Kehadiran::where('karyawan_id', $gaji->karyawan_id)
                ->whereMonth('tanggal', $gaji->periode_bulan)
                ->whereYear('tanggal', $gaji->periode_tahun)
                ->get();

            $dataAbsensi[$gaji->id_penggajian] = [
                'hadir'      => $absensi->where('status_kehadiran', 'Hadir')->count(),
                'izin'       => $absensi->where('status_kehadiran', 'Izin')->count(),
                'sakit'      => $absensi->where('status_kehadiran', 'Sakit')->count(),
                'alpa'       => $absensi->where('status_kehadiran', 'Alpa')->count(),
                'terlambat'  => $absensi->where('terlambat', true)->count(),
                'lembur_jam' => $absensi->sum('lembur_jam'),
            ];
        }


        return view('laporan.index', compact(
            'bulan','tahun','bulanTahun','tahun','totalKaryawan','totalLembur',
            'totalPotongan','totalBiayaGaji','rataGaji','data', 'dataAbsensi','tunjanganList'
        ));
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
