<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // <-- WAJIB DITAMBAHKAN UNTUK API MOBILE

class User extends Authenticatable
{
    // Masukkan HasApiTokens di sini agar fitur token Sanctum aktif
    use HasApiTokens, HasFactory, Notifiable; 

    // WAJIB: Beritahu Laravel bahwa tabel yang dipakai adalah 'tb_user', bukan 'users'
    protected $table = 'tb_user'; 

    // Kolom-kolom yang diizinkan untuk diisi secara massal (Mass Assignment)
    protected $fillable = [
        'username',
        'nama',
        'email',
        'password',
        'no_telepon',
        'jenis_kelamin',        // Sesuai dengan DB Pondasikita
        'tanggal_lahir',        // Sesuai dengan DB Pondasikita
        'alamat',               // Sesuai dengan DB Pondasikita
        'profile_picture_url',  // Sesuai dengan DB Pondasikita
        'level',
        'status',
        'status_online',        // Sesuai dengan DB Pondasikita
        'is_verified',
        'is_banned',
        'last_activity_at',
        'google_id'
    ];

    // Kolom yang disembunyikan saat data di-query (Sangat penting untuk keamanan password & token)
    protected $hidden = [
        'password',
        'remember_token',
        'google_id',
        'reset_token',
        'reset_token_expires_at'
    ];

    // Casting tipe data otomatis agar formatnya selalu benar saat ditarik dari DB
    protected $casts = [
        'email_verified_at'      => 'datetime',
        'password'               => 'hashed',
        'is_verified'            => 'boolean',
        'is_banned'              => 'boolean',
        'last_activity_at'       => 'datetime', 
        'tanggal_lahir'          => 'date',
        'reset_token_expires_at' => 'datetime',
    ];

    // =========================================================================
    // HELPER UNTUK CEK ROLE (LEVEL)
    // =========================================================================
    
    public function isAdmin() { 
        return $this->level === 'admin'; 
    }
    
    public function isSeller() { 
        return $this->level === 'seller'; 
    }
    
    public function isCustomer() { 
        return $this->level === 'customer'; 
    }

    // =========================================================================
    // RELASI DATABASE
    // =========================================================================
    
    /**
     * Relasi ke model Toko.
     * 1 User (Seller) memiliki 1 Toko.
     */
    public function toko()
    {
        // Pastikan Anda sudah membuat file model Toko.php nanti
        return $this->hasOne(Toko::class, 'user_id', 'id');
    }
}