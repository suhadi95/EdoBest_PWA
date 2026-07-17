<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operasional extends Model
{
    use HasFactory;

    protected $table = 'operasionals';
    protected $fillable = [
        'outlet_id',
        'tanggal',
        'status',
        'total_donat_harian',
    ];

    protected $dates = [
        'tanggal',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function kloters()
    {
        return $this->hasMany(Kloter::class);
    }

    public function rekap()
    {
        return $this->hasOne(Rekap::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }
}