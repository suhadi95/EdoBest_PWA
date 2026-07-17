<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $table = 'transaksis';
    protected $fillable = ['outlet_id', 'operasional_id', 'pegawai_id', 'metode_pembayaran', 'total_donat', 'total_harga', 'no_transaksi'];

    public function items()
    {
        return $this->hasMany(TransaksiItem::class);
    }

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
}