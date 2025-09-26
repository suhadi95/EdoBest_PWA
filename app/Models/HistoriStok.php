<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoriStok extends Model
{
    protected $table = 'histori_stoks';
    protected $fillable = ['outlet_id', 'jenis_stok', 'jumlah_perubahan', 'keterangan'];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}