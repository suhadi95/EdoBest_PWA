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
        'total_uang',
        'tanggal',
        'status',
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