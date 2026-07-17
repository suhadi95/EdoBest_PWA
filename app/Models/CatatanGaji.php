<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanGaji extends Model
{
    use HasFactory;

    protected $table = 'catatan_gaji';

    protected $fillable = [
        'gaji_histori_id',
        'pegawai_id',
        'jenis',
        'jumlah',
        'catatan',
    ];

    public function gajiHistori()
    {
        return $this->belongsTo(GajiHistori::class, 'gaji_histori_id');
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }
}
