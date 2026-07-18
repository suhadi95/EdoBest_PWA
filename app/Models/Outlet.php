<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlets';
    protected $fillable = ['nama', 'alamat', 'biaya_listrik_harian'];

    protected $casts = [
        'biaya_listrik_harian' => 'integer',
    ];

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class);
    }

    public function operasionals()
    {
        return $this->hasMany(Operasional::class);
    }

    public function listrikPembayarans()
    {
        return $this->hasMany(ListrikPembayaran::class);
    }

    public function stokOutlet()
    {
        return $this->hasOne(StokOutlet::class);
    }

    public function historiStoks()
    {
        return $this->hasMany(HistoriStok::class);
    }
}
