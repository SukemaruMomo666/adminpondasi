<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class Barang extends Model
{
    use HasFactory;

    // 1. Nama tabel di database
    protected $table = 'tb_barang';

    // 2. Lindungi ID, sisanya boleh diisi massal
    protected $guarded = ['id'];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($barang) {
            if (empty($barang->slug)) {
                $barang->slug = static::generateUniqueSlug($barang->nama_barang);
            }
        });

        static::updating(function ($barang) {
            if ($barang->isDirty('nama_barang') && empty($barang->slug)) {
                $barang->slug = static::generateUniqueSlug($barang->nama_barang);
            }
        });
    }

    private static function generateUniqueSlug($nama)
    {
        $slug = Str::slug($nama);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    // ================= RELASI =================

    // Relasi: Barang ini dijual oleh siapa? (Oleh Toko)
    public function toko()
    {
        return $this->belongsTo(Toko::class, 'toko_id', 'id');
    }

    // Relasi: Barang ini masuk kategori apa?
    // Asumsi Anda nanti buat model Kategori untuk tabel 'tb_kategori'
    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id', 'id');
    }

    // Relasi: Barang ini gambarnya apa saja?
    // Asumsi Anda nanti buat model GambarBarang untuk tabel 'tb_gambar_barang'
    public function gambar()
    {
        return $this->hasMany(GambarBarang::class, 'barang_id', 'id');
    }
}