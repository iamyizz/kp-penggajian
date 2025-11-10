<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Jabatan
        Schema::create('jabatan', function (Blueprint $table) {
            $table->id('id_jabatan');
            $table->string('nama_jabatan', 100);
            $table->decimal('gaji_pokok', 12, 2);
            $table->decimal('tunjangan_jabatan', 12, 2)->default(0);
            $table->timestamps();
        });

        // 2. Tabel Karyawan
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id('id_karyawan');
            $table->string('nip', 20)->unique();
            $table->string('nama', 100);
            $table->foreignId('jabatan_id')->constrained('jabatan', 'id_jabatan')->onDelete('cascade');
            $table->date('tanggal_masuk')->nullable();
            $table->enum('status_karyawan', ['Tetap', 'Kontrak', 'Magang'])->default('Kontrak');
            $table->string('rekening_bank', 50)->nullable();
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        // 3. Tabel Kehadiran
        Schema::create('kehadiran', function (Blueprint $table) {
            $table->id('id_kehadiran');
            $table->foreignId('karyawan_id')->constrained('karyawan', 'id_karyawan')->onDelete('cascade');
            $table->date('tanggal');
            $table->enum('status_kehadiran', ['Hadir', 'Izin', 'Sakit', 'Alpa']);
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            $table->boolean('terlambat')->default(false);
            $table->decimal('lembur_jam', 4, 2)->default(0);
            $table->timestamps();
        });

        // 4. Tabel Tunjangan Kehadiran & Makan
        Schema::create('tunjangan_kehadiran_makan', function (Blueprint $table) {
            $table->id('id_tkm');
            $table->foreignId('karyawan_id')->constrained('karyawan', 'id_karyawan')->onDelete('cascade');
            $table->unsignedTinyInteger('bulan');
            $table->year('tahun');
            $table->integer('total_hadir')->default(0);
            $table->integer('total_terlambat')->default(0);
            $table->decimal('tunjangan_harian', 12, 2)->default(0);
            $table->decimal('potongan_terlambat', 12, 2)->default(0);
            $table->decimal('total_tunjangan', 12, 2)->default(0);
            $table->timestamps();
        });

        // 5. Tabel Penggajian
        Schema::create('penggajian', function (Blueprint $table) {
            $table->id('id_penggajian');
            $table->foreignId('karyawan_id')->constrained('karyawan', 'id_karyawan')->onDelete('cascade');
            $table->unsignedTinyInteger('periode_bulan');
            $table->year('periode_tahun');
            $table->decimal('gaji_pokok', 12, 2)->default(0);
            $table->decimal('tunjangan_jabatan', 12, 2)->default(0);
            $table->decimal('tunjangan_kehadiran_makan', 12, 2)->default(0);
            $table->decimal('lembur', 12, 2)->default(0);
            $table->decimal('potongan_absen', 12, 2)->default(0);
            $table->decimal('potongan_bpjs', 12, 2)->default(0);
            $table->decimal('total_gaji', 12, 2)->default(0);
            $table->date('tanggal_proses');
            $table->timestamps();
        });

        // 6. Tabel Parameter Penggajian (Setting Global)
        Schema::create('parameter_penggajian', function (Blueprint $table) {
            $table->id('id_param');
            $table->string('nama_param', 50);
            $table->decimal('nilai', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter_penggajian');
        Schema::dropIfExists('penggajian');
        Schema::dropIfExists('tunjangan_kehadiran_makan');
        Schema::dropIfExists('kehadiran');
        Schema::dropIfExists('karyawan');
        Schema::dropIfExists('jabatan');
    }
};
