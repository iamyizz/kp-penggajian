<?php

namespace App\Imports;

use App\Models\Kehadiran;
use App\Models\Karyawan;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AbsensiImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cari karyawan berdasarkan NIP
        $karyawan = Karyawan::where('nip', $row['nip'])->first();

        if (!$karyawan) {
            return null;
        }

        // Parse tanggal dengan berbagai format
        $tanggal = $this->parseDate($row['tanggal']);

        if (!$tanggal) {
            return null; // Skip jika tanggal tidak valid
        }

        // Cek duplikat
        $existing = Kehadiran::where('karyawan_id', $karyawan->id_karyawan)
                            ->where('tanggal', $tanggal)
                            ->first();

        if ($existing) {
            return null;
        }

        // Parse jam (handle Excel time format)
        $jamMasuk = $this->parseTime($row['jam_masuk'] ?? null);
        $jamKeluar = $this->parseTime($row['jam_keluar'] ?? null);

        return new Kehadiran([
            'karyawan_id'      => $karyawan->id_karyawan,
            'tanggal'          => $tanggal,
            'status_kehadiran' => $row['status_kehadiran'],
            'jam_masuk'        => $jamMasuk,
            'jam_keluar'       => $jamKeluar,
            'terlambat'        => $row['terlambat'] ?? 0,
            'lembur_jam'       => $row['lembur_jam'] ?? 0.00,
        ]);
    }

    /**
     * Parse tanggal dari berbagai format
     */
    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        // Trim whitespace
        $value = trim($value);

        try {
            // Jika Excel serial number (angka)
            if (is_numeric($value)) {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            }

            // Jika format YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                return $value; // Sudah benar, langsung return
            }

            // Jika format DD-MM-YYYY
            if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
                $parts = explode('-', $value);
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }

            // Jika format DD/MM/YYYY
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $value)) {
                $parts = explode('/', $value);
                return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
            }

            // Jika format YYYY/MM/DD
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $value)) {
                return str_replace('/', '-', $value);
            }

            // Fallback: coba parse dengan Carbon
            return Carbon::parse($value)->format('Y-m-d');

        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Parse jam dari berbagai format
     */
    private function parseTime($value)
    {
        if (empty($value)) {
            return null;
        }

        // Trim whitespace
        $value = trim($value);

        try {
            // Jika Excel serial number untuk waktu (angka desimal)
            if (is_numeric($value)) {
                $seconds = $value * 86400; // Convert to seconds
                $hours = floor($seconds / 3600);
                $minutes = floor(($seconds % 3600) / 60);
                $secs = $seconds % 60;
                return sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
            }

            // Jika format HH:MM (tanpa detik)
            if (preg_match('/^\d{2}:\d{2}$/', $value)) {
                return $value . ':00';
            }

            // Jika format HH:MM:SS
            if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $value)) {
                return $value;
            }

            // Fallback
            return Carbon::parse($value)->format('H:i:s');

        } catch (\Exception $e) {
            return null;
        }
    }
}
