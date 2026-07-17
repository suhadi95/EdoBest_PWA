<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'nominal',
        'keterangan',
        'status',
        'status_pembayaran',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
