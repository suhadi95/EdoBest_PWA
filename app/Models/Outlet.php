<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $table = 'outlets';
    protected $fillable = ['nama', 'alamat', 'pegawai_id'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function operasionals()
    {
        return $this->hasMany(Operasional::class);
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
