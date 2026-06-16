<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_banding_akun', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('alasan_banding');
            $table->string('bukti_pendukung')->nullable();
            $table->enum('status', ['pending', 'disetujui', 'ditolak'])->default('pending');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            // Link ke tb_user (Laravel default users table usually)
            // But let's check the schema again, tb_user uses id int(11)
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_banding_akun');
    }
};
