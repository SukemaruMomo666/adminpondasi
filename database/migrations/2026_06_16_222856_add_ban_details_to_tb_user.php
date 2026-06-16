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
        Schema::table('tb_user', function (Blueprint $table) {
            $table->enum('ban_type', ['none', 'ringan', 'berat'])->default('none')->after('is_banned');
            $table->text('ban_reason')->nullable()->after('ban_type');
            $table->timestamp('banned_until')->nullable()->after('ban_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn(['ban_type', 'ban_reason', 'banned_until']);
        });
    }
};
