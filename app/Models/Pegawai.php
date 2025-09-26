<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';

    protected $fillable = [
        'nama',
        'username',
        'gaji_harian',
        'bonus_nominal',
        'bonus_syarat',
        'role',
    ];

    public function outlets()
    {
        return $this->hasMany(Outlet::class, 'pegawai_id');
    }
}
