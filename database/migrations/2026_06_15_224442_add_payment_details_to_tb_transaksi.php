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
        Schema::table('tb_transaksi', function (Blueprint $row) {
            $row->decimal('bayar', 15, 2)->nullable()->after('total_final');
            $row->decimal('kembali', 15, 2)->nullable()->after('bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $row) {
            $row->dropColumn(['bayar', 'kembali']);
        });
    }
};
