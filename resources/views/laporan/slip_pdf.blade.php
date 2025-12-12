<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Slip Gaji</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
            color: #333;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #2c3e50;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            margin-top: 18px;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 1px solid #888;
            color: #2c3e50;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        td, th {
            padding: 6px;
            border: 1px solid #ccc;
        }

        th {
            background: #f3f6f9;
            font-weight: bold;
        }

        .summary-box {
            margin-top: 15px;
            padding: 10px;
            background: #e8f5e9;
            border-left: 5px solid #43a047;
        }

        .summary-box h2 {
            margin: 0;
            font-size: 22px;
            color: #2e7d32;
        }

        .footer-sign {
            margin-top: 50px;
            text-align: right;
        }

        .footer-sign small {
            color: #777;
        }
    </style>
</head>

<body>

    <div class="title">
        SLIP GAJI KARYAWAN
        <div style="font-size: 12px; margin-top: -3px;">
            Periode: {{ date('F Y', strtotime($gaji->periode_tahun.'-'.$gaji->periode_bulan.'-01')) }}
        </div>
    </div>

    {{-- =============================== --}}
    {{-- 1. INFORMASI KARYAWAN --}}
    {{-- =============================== --}}
    <div class="section-title">Informasi Karyawan</div>

    <table>
        <tr>
            <th width="30%">Nama</th>
            <td>{{ $gaji->karyawan->nama }}</td>
        </tr>
        <tr>
            <th>NIP / NIK</th>
            <td>{{ $gaji->karyawan->nip ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jenis Kelamin</th>
            <td>{{ $gaji->karyawan->jenis_kelamin ?? '-' }}</td>
        </tr>
        <tr>
            <th>Jabatan</th>
            <td>{{ $gaji->karyawan->jabatan->nama_jabatan }}</td>
        </tr>
        <tr>
            <th>Tanggal Masuk</th>
            <td>{{ $gaji->karyawan->tanggal_masuk ?? '-' }}</td>
        </tr>
        <tr>
            <th>Masa Kerja</th>
            <td>
                @php
                    if ($gaji->karyawan->tanggal_masuk) {
                        $mulai = new DateTime($gaji->karyawan->tanggal_masuk);
                        $now = new DateTime();
                        $interval = $mulai->diff($now);
                        echo $interval->y . ' Tahun ' . $interval->m . ' Bulan';
                    } else echo '-';
                @endphp
            </td>
        </tr>
    </table>


    {{-- =============================== --}}
    {{-- 2. GAJI POKOK + TUNJANGAN --}}
    {{-- =============================== --}}
    <div class="section-title">Rincian Gaji & Tunjangan</div>

    <table>
        <tr>
            <th>Gaji Pokok</th>
            <td class="text-end">Rp {{ number_format($gaji->gaji_pokok) }}</td>
        </tr>

        <tr>
            <th>Tunjangan Jabatan</th>
            <td>Rp {{ number_format($gaji->tunjangan_jabatan) }}</td>
        </tr>

        @php
            $tkm = $tunjanganList[$gaji->karyawan_id] ?? null;
        @endphp

        <tr>
            <th>Tunjangan Kehadiran</th>
            <td>Rp {{ number_format($tkm->tunjangan_harian ?? 0) }}</td>
        </tr>

        <tr>
            <th>Total Tunjangan</th>
            <td>
                Rp {{ number_format($gaji->tunjangan_jabatan + $gaji->tunjangan_kehadiran_makan) }}
            </td>
        </tr>

        <tr>
            <th>Lembur</th>
            <td>Rp {{ number_format($gaji->lembur ?? 0) }}</td>
        </tr>
    </table>


    {{-- =============================== --}}
    {{-- 3. DETAIL KEHADIRAN --}}
    {{-- =============================== --}}
    <div class="section-title">Rincian Kehadiran</div>

    <table>
        <tr>
            <th>Hadir</th>
            <th>Izin</th>
            <th>Sakit</th>
            <th>Alpa</th>
            <th>Terlambat</th>
            <th>Lembur (Jam)</th>
        </tr>
        <tr>
            <td>{{ $absensi['hadir'] }}</td>
            <td>{{ $absensi['izin'] }}</td>
            <td>{{ $absensi['sakit'] }}</td>
            <td>{{ $absensi['alpa'] }}</td>
            <td>{{ $absensi['terlambat'] }}</td>
            <td>{{ $absensi['lembur_jam'] }}</td>
        </tr>
    </table>


    {{-- =============================== --}}
    {{-- 4. POTONGAN --}}
    {{-- =============================== --}}
    <div class="section-title">Rincian Potongan</div>

    <table>
        <tr><th>Potongan Absen</th><td>Rp {{ number_format($gaji->potongan_absen) }}</td></tr>
        <tr><th>BPJS</th><td>Rp {{ number_format($gaji->potongan_bpjs) }}</td></tr>
        <tr><th>Potongan Lain-lain</th><td>Rp {{ number_format($gaji->potongan_lain ?? 0) }}</td></tr>

        <tr>
            <th><b>Total Potongan</b></th>
            <td><b>Rp {{ number_format(
                $gaji->potongan_absen +
                $gaji->potongan_bpjs +
                ($gaji->potongan_terlambat ?? 0) +
                ($gaji->potongan_lain ?? 0)
            ) }}</b></td>
        </tr>
    </table>


    {{-- =============================== --}}
    {{-- 5. RINGKASAN GAJI --}}
    {{-- =============================== --}}
    @php
        $totalTunjangan =
            $gaji->tunjangan_jabatan +
            $gaji->tunjangan_kehadiran_makan +
            ($gaji->tunjangan_transport ?? 0) +
            ($gaji->tunjangan_keluarga ?? 0);

        $totalPotongan =
            $gaji->potongan_absen +
            $gaji->potongan_bpjs +
            ($gaji->potongan_terlambat ?? 0) +
            ($gaji->potongan_lain ?? 0);

        $totalLembur = $gaji->lembur ?? 0;

        $gajiBersih = $gaji->gaji_pokok + $totalTunjangan + $totalLembur;

        $takeHomePay = $gajiBersih - $totalPotongan;
    @endphp

    <div class="section-title">Ringkasan Gaji</div>

    <table>
        <tr><th>Gaji Pokok</th><td>Rp {{ number_format($gaji->gaji_pokok) }}</td></tr>
        <tr><th>Total Tunjangan</th><td>Rp {{ number_format($totalTunjangan) }}</td></tr>
        <tr><th>Lembur</th><td>Rp {{ number_format($totalLembur) }}</td></tr>
        <tr><th>Gaji Bersih</th><td>Rp {{ number_format($gajiBersih) }}</td></tr>
        <tr><th>Total Potongan</th><td>Rp {{ number_format($totalPotongan) }}</td></tr>

        <tr style="background: #e8f5e9; font-weight: bold;">
            <th>Take Home Pay</th>
            <td>Rp {{ number_format($takeHomePay) }}</td>
        </tr>
    </table>

    <div class="summary-box">
        <small>Total Take Home Pay</small>
        <h2>Rp {{ number_format($gaji->total_gaji) }}</h2>
    </div>

    <div class="footer-sign">
        <p><strong>{{ Auth::user()->name ?? 'Admin' }}</strong></p>
        <small>HRD / Finance</small>
    </div>

</body>
</html>
