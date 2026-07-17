<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rekap extends Model
{
    use HasFactory;

    protected $table = 'rekaps';
    protected $fillable = [
        'outlet_id',
        'operasional_id',
        'pegawai_id',
        'total_donat_terjual',
        'sisa_mika',
        'sisa_dus1',
        'sisa_dus2',
        'sisa_dus3',
        'sisa_box',
        'sisa_box12',
        'sisa_lilin',
        'used_mika',
        'used_dus1',
        'used_dus2',
        'used_dus3',
        'used_box',
        'used_box12',
        'used_lilin',
        'total_uang_penjualan',
        'total_uang',
        'total_tunai',
        'total_qris',
        'total_transfer',
        'total_maxim',
        'total_grabfood',
        'total_gofood',
        'cash_di_pegawai',
        'tanggal',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function operasional()
    {
        return $this->belongsTo(Operasional::class);
    }

    public function catatanOperasionals()
    {
        return $this->hasMany(CatatanOperasional::class);
    }
}
