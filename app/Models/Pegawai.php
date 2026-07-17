<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Pegawai extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'nama',
        'username',
        'password',
        'role',
        'gaji_harian',
        'tambahan_gaji_1',
        'target_1',
        'tambahan_gaji_2',
        'target_2',
        'tambahan_gaji_3',
        'target_3',
        'tambahan_gaji_4',
        'target_4',
        'bonus_nominal',
        'bonus_syarat',
        'outlet_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}
