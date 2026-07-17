<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $table = 'transaksi_items';
    protected $fillable = [
        'transaksi_id',
        'kemasan',
        'jumlah',
        'tipe',
        'harga',
        'subtotal',
    ];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}