<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaItem extends Model
{
    protected $table = 'harga_items';
    protected $fillable = ['nama_item', 'harga_original', 'harga_klasik'];
}