<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tb_pengajuan_tier', function (Blueprint $table) {
            $table->id();
            $table->integer('toko_id');
            $table->enum('tier_saat_ini', ['regular', 'power_merchant', 'official_store']);
            $table->enum('tier_tujuan', ['regular', 'power_merchant', 'official_store']);
            $table->enum('status', ['pending', 'approved', 'rejected', 'revision'])->default('pending');
            $table->text('catatan_penjual')->nullable();
            $table->text('alasan_admin')->nullable();
            $table->json('metadata_syarat')->nullable(); // Snapshot rating, total order, dll
            $table->timestamps();

            // Link ke tb_toko
            $table->foreign('toko_id')->references('id')->on('tb_toko')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_pengajuan_tier');
    }
};
