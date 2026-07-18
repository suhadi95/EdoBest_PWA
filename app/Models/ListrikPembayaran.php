<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListrikPembayaran extends Model
{
    protected $table = 'listrik_pembayarans';

    protected $fillable = [
        'outlet_id',
        'pegawai_id',
        'jumlah_hari',
        'total_nominal',
        'dibayar_at',
        'keterangan',
    ];

    protected $casts = [
        'dibayar_at' => 'datetime',
        'jumlah_hari' => 'integer',
        'total_nominal' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function operasionals()
    {
        return $this->hasMany(Operasional::class, 'listrik_pembayaran_id');
    }
}
