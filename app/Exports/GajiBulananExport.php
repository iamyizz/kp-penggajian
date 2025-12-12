<?php

namespace App\Exports;

use App\Models\Penggajian;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GajiBulananExport implements FromCollection, WithHeadings
{
    protected $bulan;
    protected $tahun;

    public function __construct(int $bulan, int $tahun)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection()
    {
        $rows = Penggajian::with('karyawan.jabatan')
            ->where('periode_bulan', $this->bulan)
            ->where('periode_tahun', $this->tahun)
            ->orderBy('karyawan_id')
            ->get()
            ->map(function($item) {
                return [
                    'nip' => $item->karyawan->nip ?? '-',
                    'nama' => $item->karyawan->nama ?? '-',
                    'jabatan' => $item->karyawan->jabatan->nama_jabatan ?? '-',
                    'gaji_pokok' => $item->gaji_pokok,
                    'tunjangan_jabatan' => $item->tunjangan_jabatan,
                    'tunjangan_kehadiran_makan' => $item->tunjangan_kehadiran_makan,
                    'lembur' => $item->lembur,
                    'potongan_absen' => $item->potongan_absen,
                    'potongan_bpjs' => $item->potongan_bpjs,
                    'total_gaji' => $item->total_gaji,
                ];
            });

        return collect($rows);
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama',
            'Jabatan',
            'Gaji Pokok',
            'Tunjangan Jabatan',
            'Tunjangan Kehadiran & Makan',
            'Lembur',
            'Potongan Absen',
            'Potongan BPJS',
            'Total Gaji',
        ];
    }
}
