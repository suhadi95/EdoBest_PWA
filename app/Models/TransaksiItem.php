<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiItem extends Model
{
    protected $table = 'transaksi_items';
    protected $fillable = ['transaksi_id', 'kemasan', 'jumlah', 'tipe', 'donat_per_item', 'total_harga'];

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }
}