<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parameter_penggajian', function (Blueprint $table) {
            $table->id('id_param');
            $table->string('nama_param', 100);
            $table->string('key', 100)->unique(); // untuk pengambilan mudah
            $table->decimal('nilai', 12, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parameter_penggajian');
    }
};
