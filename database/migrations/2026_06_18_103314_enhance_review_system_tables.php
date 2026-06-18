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
        // 1. Centralize and enhance tb_review_produk
        Schema::table('tb_review_produk', function (Blueprint $table) {
            $table->text('balasan_seller')->nullable()->after('gambar_ulasan');
            $table->timestamp('waktu_balasan')->nullable()->after('balasan_seller');
            $table->boolean('is_hidden')->default(false)->after('waktu_balasan');
            $table->boolean('is_rewarded')->default(false)->after('is_hidden');
            $table->string('video_ulasan')->nullable()->after('gambar_ulasan');
        });

        // 2. Reward points for users
        Schema::table('tb_user', function (Blueprint $table) {
            $table->integer('poin')->default(0)->after('status_online');
        });

        // 3. Cached rating for products
        Schema::table('tb_barang', function (Blueprint $table) {
            $table->decimal('rating_rata', 3, 2)->default(0)->after('is_active');
            $table->integer('jumlah_ulasan')->default(0)->after('rating_rata');
        });

        // 4. Cached rating for stores
        Schema::table('tb_toko', function (Blueprint $table) {
            $table->decimal('rating_toko', 3, 2)->default(0)->after('tier_toko');
            $table->integer('jumlah_ulasan_toko')->default(0)->after('rating_toko');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_review_produk', function (Blueprint $table) {
            $table->dropColumn(['balasan_seller', 'waktu_balasan', 'is_hidden', 'is_rewarded', 'video_ulasan']);
        });

        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropColumn('poin');
        });

        Schema::table('tb_barang', function (Blueprint $table) {
            $table->dropColumn(['rating_rata', 'jumlah_ulasan']);
        });

        Schema::table('tb_toko', function (Blueprint $table) {
            $table->dropColumn(['rating_toko', 'jumlah_ulasan_toko']);
        });
    }
};
