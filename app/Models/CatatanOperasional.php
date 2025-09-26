<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanOperasional extends Model
{
    use HasFactory;

    protected $fillable = [
        'outlet_id',
        'operasional_id',
        'pegawai_id',
        'rekap_id',
        'jenis',
        'jumlah',
        'catatan',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function operasional()
    {
        return $this->belongsTo(Operasional::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function rekap()
    {
        return $this->belongsTo(Rekap::class);
    }
}