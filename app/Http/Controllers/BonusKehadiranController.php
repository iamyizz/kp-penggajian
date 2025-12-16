<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BonusKehadiran;
use App\Models\Karyawan;
use App\Models\ParameterPenggajian;
use App\Models\Kehadiran;

class BonusKehadiranController extends Controller
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

        $data = BonusKehadiran::with('karyawan')
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->orderBy('karyawan_id')
                    ->get();

        return view('bonus.index', [
            'data' => $data,
            'bulan' => $bulanTahun,
        ]);
    }

    public function proses(Request $req)
    {
        $bulan = $req->bulan;
        $tahun = $req->tahun;

        if (! $bulan || ! $tahun) {
            return response()->json(['status' => false, 'message' => 'Bulan/tahun wajib dikirim.']);
        }

        $bonus_nominal = ParameterPenggajian::where('key', 'bonus_kehadiran_nominal')->value('nilai') ?? 0;

        $karyawan = Karyawan::all();

        foreach ($karyawan as $k) {

            $query = Kehadiran::where('karyawan_id', $k->id_karyawan)
                              ->whereMonth('tanggal', $bulan)
                              ->whereYear('tanggal', $tahun);

            $hadir = (clone $query)->where('status_kehadiran', 'Hadir')->count();
            $izin  = (clone $query)->where('status_kehadiran', 'Izin')->count();
            $sakit = (clone $query)->where('status_kehadiran', 'Sakit')->count();
            // pastikan value status di DB sama (Alpa / Alpha / Alpa). Gunakan yang konsisten.
            $alpha = (clone $query)->where('status_kehadiran', 'Alpa')->count();
            $terlambat = (clone $query)->where('terlambat', '>', 0)->count();

            // LOGIKA BONUS (aturan B: hadir semua dan tidak ada terlambat)
            $boleh_bonus = ($izin == 0 && $sakit == 0 && $alpha == 0 && $terlambat == 0);
            $nilai = $boleh_bonus ? $bonus_nominal : 0;

            BonusKehadiran::updateOrCreate(
                [
                    'karyawan_id' => $k->id_karyawan,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                ],
                [
                    'total_hadir' => $hadir,
                    'total_izin'  => $izin,
                    'total_sakit' => $sakit,
                    'total_alpha' => $alpha,
                    'total_terlambat' => $terlambat,
                    'dapat_bonus' => $boleh_bonus,
                    'nominal_bonus' => $nilai,
                ]
            );
        }

        return response()->json([
            'status' => true,
            'message' => 'Bonus kehadiran berhasil diproses!'
        ]);
    }
}
