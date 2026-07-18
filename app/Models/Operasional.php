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
        'biaya_listrik',
        'listrik_pembayaran_id',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'biaya_listrik' => 'integer',
    ];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    public function listrikPembayaran()
    {
        return $this->belongsTo(ListrikPembayaran::class, 'listrik_pembayaran_id');
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