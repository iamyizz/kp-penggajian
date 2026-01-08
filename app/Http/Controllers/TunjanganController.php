<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\TunjanganKehadiranMakan;
use App\Models\ParameterPenggajian;
use Illuminate\Http\Request;

class TunjanganController extends Controller
{
    public function index(Request $req)
    {
        $bulanTahun = request('bulan');

        if ($bulanTahun) {
            list($tahun, $bulan) = explode('-', $bulanTahun);
        } else {
            $bulan = date('m');
            $tahun = date('Y');
            $bulanTahun = $tahun . '-' . $bulan;
        }

        $data = TunjanganKehadiranMakan::with('karyawan')
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->get();

        // flag: sudah diproses untuk periode ini?
        $sudahDiproses = TunjanganKehadiranMakan::where('bulan', (int)$bulan)
            ->where('tahun', (int)$tahun)
            ->exists();

        // ambil tarif langsung dari parameter
        $tarif_makan = ParameterPenggajian::where('key','tunjangan_makan_per_hari')->value('nilai');
        $tarif_potongan = ParameterPenggajian::where('key','potongan_telat_per_menit')->value('nilai');

        return view('tunjangan.index', [
            'data' => $data,
            'bulan' => $bulanTahun,
            'tarif_makan' => $tarif_makan,
            'tarif_potongan' => $tarif_potongan,
            'sudahDiproses' => $sudahDiproses,
        ]);
    }

    public function proses(Request $r)
    {
        $bulan = $r->bulan;
        $tahun = $r->tahun;

        if (!$bulan || !$tahun) {
            return response()->json([
                'status' => false,
                'message' => 'Bulan dan tahun tidak diterima.'
            ]);
        }

        // cek apakah sudah diproses sebelumnya
        if (TunjanganKehadiranMakan::where('bulan', $bulan)->where('tahun', $tahun)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah memproses Tunjangan bulan ini.'
            ], 409);
        }
        // ambil parameter
        $tarif_makan = ParameterPenggajian::where('key', 'tunjangan_makan_per_hari')->value('nilai');
        $potongan_terlambat = ParameterPenggajian::where('key','potongan_telat_per_menit')->value('nilai') ?? 0;

        $karyawan = Karyawan::all();

        foreach ($karyawan as $k) {

            $hadir = Kehadiran::where('karyawan_id', $k->id_karyawan)
                        ->whereMonth('tanggal', $bulan)
                        ->whereYear('tanggal', $tahun)
                        ->where('status_kehadiran', 'Hadir')
                        ->count();

            $terlambat = Kehadiran::where('karyawan_id', $k->id_karyawan)
                        ->whereMonth('tanggal', $bulan)
                        ->whereYear('tanggal', $tahun)
                        ->where('terlambat', true)
                        ->count();

            $tunjangan = $hadir * $tarif_makan;
            $potongan  = $terlambat * $potongan_terlambat;
            $total     = $tunjangan - $potongan;

            TunjanganKehadiranMakan::updateOrCreate(
                [
                    'karyawan_id' => $k->id_karyawan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'total_hadir' => $hadir,
                    'total_terlambat' => $terlambat,
                    'tunjangan_harian' => $tunjangan,
                    'potongan_terlambat' => $potongan,
                    'total_tunjangan' => $total,
                ]
            );
        }

        // Format: YYYY-MM untuk redirect filter
        $redirectFilter = $tahun . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT);

        return response()->json([
            'status' => true,
            'message' => 'Tunjangan berhasil diproses!',
            'redirect_filter' => $redirectFilter
        ]);
    }
}
