<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kloter extends Model
{
    use HasFactory;

    protected $table = 'kloters';
    protected $fillable = [
        'operasional_id',
        'jumlah_donat',
        'jumlah_mika',
        'jumlah_dus1',
        'jumlah_dus2',
        'jumlah_dus3',
        'jumlah_box',
    ];

    public function operasional()
    {
        return $this->belongsTo(Operasional::class);
    }
}