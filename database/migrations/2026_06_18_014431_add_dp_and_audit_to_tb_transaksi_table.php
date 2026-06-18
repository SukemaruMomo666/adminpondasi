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
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->timestamp('dp_expired_at')->nullable()->after('sisa_tagihan');
            $table->unsignedBigInteger('completed_by')->nullable()->after('status_pesanan_global');
            $table->timestamp('completed_at')->nullable()->after('completed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropColumn(['dp_expired_at', 'completed_by', 'completed_at']);
        });
    }
};
