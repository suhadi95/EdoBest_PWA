<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokOutlet extends Model
{
    protected $table = 'stok_outlets';
    protected $fillable = ['outlet_id', 'stok_mika', 'stok_dus1', 'stok_dus2', 'stok_dus3', 'stok_box'];

    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
}