<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\Kehadiran;
use App\Models\TunjanganKehadiranMakan;
use App\Models\ParameterPenggajian;
use App\Models\BonusKehadiran;
use App\Models\Penggajian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PenggajianController extends Controller
{
    /**
     * Tampilkan daftar penggajian (index).
     */
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

        $data = Penggajian::with('karyawan')
            ->where('periode_bulan', (int)$bulan)
            ->where('periode_tahun', (int)$tahun)
            ->orderBy('karyawan_id')
            ->get();
       
            // flag: sudah diproses untuk periode ini?
        $sudahDiproses = Penggajian::where('periode_bulan', (int)$bulan)
            ->where('periode_tahun', (int)$tahun)
            ->exists();    

        return view('penggajian.index', [
            'data' => $data,
            'bulan' => $bulanTahun,
            'sudahDiproses' => $sudahDiproses,
        ]);
    }

    /**
     * Proses penggajian untuk satu periode (bulan & tahun).
     * Request body: { bulan: '02', tahun: '2025' } (bulan = 2 digits or number)
     */
    public function proses(Request $request)
    {
        $request->validate([
            'bulan' => 'required|numeric|min:1|max:12',
            'tahun' => 'required|numeric|min:2000',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;

         // cek apakah sudah diproses sebelumnya
        if (Penggajian::where('periode_bulan', $bulan)->where('periode_tahun', $tahun)->exists()) {
            return response()->json([
                'status' => false,
                'message' => 'Anda sudah memproses penggajian bulan ini.'
            ], 409);
        }

        // Ambil parameter penting (default fallback bila tidak ada)
        $lemburPerJam = ParameterPenggajian::where('key', 'lembur_per_jam')->value('nilai') ?? 0;
        // potongan per hari alpa
        $potonganAlpaPerHari = ParameterPenggajian::where('key', 'potongan_alpa')->value('nilai') ?? 0;
        // potongan bpjs dalam persen (misal 1.0 = 1%)
        $potonganBpjsPercent = ParameterPenggajian::where('key', 'potongan_bpjs_persen')->value('nilai') ?? 0;

        // Ambil semua karyawan aktif (atau semua)
        $karyawans = Karyawan::where('aktif', true)->get();

        $format = Carbon::createFromDate($tahun, $bulan, 1)
            ->locale('id')
            ->translatedFormat('F Y');


        DB::beginTransaction();
        try {
            foreach ($karyawans as $k) {
                // Basic salary & tunjangan jabatan dari relasi jabatan
                $jab = $k->jabatan; // pastikan relasi ada di model Karyawan
                $gajiPokok = $jab->gaji_pokok ?? 0;
                $tunjanganJabatan = $jab->tunjangan_jabatan ?? 0;

                // Tunjangan Kehadiran & Makan (precomputed table)
                $tkm = TunjanganKehadiranMakan::where('karyawan_id', $k->id_karyawan)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->first();

                $tunjanganKehadiranMakan = $tkm->total_tunjangan ?? 0;

                // Lembur: jumlahkan lembur_jam pada tabel kehadiran
                $totalLemburJam = Kehadiran::where('karyawan_id', $k->id_karyawan)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->sum('lembur_jam');

                $lembur = round($totalLemburJam * $lemburPerJam, 2);

                // Potongan absen (alpa): hitung jumlah Alpa dan kalikan
                $totalAlpa = Kehadiran::where('karyawan_id', $k->id_karyawan)
                    ->whereMonth('tanggal', $bulan)
                    ->whereYear('tanggal', $tahun)
                    ->where('status_kehadiran', 'Alpa')
                    ->count();

                $potonganAbsen = $totalAlpa * $potonganAlpaPerHari;

                // Potongan BPJS (persen): asumsi dipotong dari (gaji pokok + tunjangan jabatan)
                $bpjs_base = $gajiPokok + $tunjanganJabatan;
                $potonganBpjs = round(($potonganBpjsPercent / 100) * $bpjs_base, 2);

                // Bonus: ambil dari table bonus_kehadiran jika ada
                $bonusNominal = BonusKehadiran::where('karyawan_id', $k->id_karyawan)
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->value('nominal_bonus') ?? 0;

                // Total gaji per formula:
                // total = gajiPokok + tunjanganJabatan + tunjanganKehadiranMakan + lembur + bonus - potonganAbsen - potonganBpjs
                $totalGaji = round(
                    $gajiPokok
                    + $tunjanganJabatan
                    + $tunjanganKehadiranMakan
                    + $lembur
                    + $bonusNominal
                    - $potonganAbsen
                    - $potonganBpjs,
                    2
                );

                // Simpan/Update ke tabel penggajian
                Penggajian::updateOrCreate(
                    [
                        'karyawan_id' => $k->id_karyawan,
                        'periode_bulan' => $bulan,
                        'periode_tahun' => $tahun,
                    ],
                    [
                        'gaji_pokok' => $gajiPokok,
                        'tunjangan_jabatan' => $tunjanganJabatan,
                        'tunjangan_kehadiran_makan' => $tunjanganKehadiranMakan,
                        'lembur' => $lembur,
                        'bonus' => $bonusNominal,
                        'potongan_absen' => $potonganAbsen,
                        'potongan_bpjs' => $potonganBpjs,
                        'total_gaji' => $totalGaji,
                        'tanggal_proses' => Carbon::now()->toDateString(),
                    ]
                );
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Penggajian bulan {$format} berhasil diproses."
            ]);

        } catch (\Throwable $ex) {
            DB::rollBack();
            // log error jika perlu
            \Log::error('Error proses penggajian: '.$ex->getMessage());

            return response()->json([
                'status' => false,
                'message' => 'Gagal memproses penggajian: '.$ex->getMessage()
            ], 500);
        }
    }
}
