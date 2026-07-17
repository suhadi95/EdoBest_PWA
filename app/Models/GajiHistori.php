<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GajiHistori extends Model
{
    use HasFactory;

    protected $table = 'gaji_histori';

    protected $fillable = [
        'pegawai_id',
        'tanggal_gaji',
        'periode_mulai',
        'periode_akhir',
        'periode_keterangan',
        'gaji_harian',
        'kasbon',
        'gaji_bersih',
        'gaji_total', // untuk backward compatibility
        'status',
        'rincian_gaji_harian',
    ];

    protected $casts = [
        'tanggal_gaji' => 'date',
        'periode_mulai' => 'date',
        'periode_akhir' => 'date',
        'rincian_gaji_harian' => 'array',
    ];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function catatanGaji()
    {
        return $this->hasMany(CatatanGaji::class, 'gaji_histori_id');
    }
}
