<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HargaItem;

class HargaItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama_item' => 'mika', 'harga_reguler' => 5000, 'harga_classic' => 5000, 'harga_costum' => 7000],
            ['nama_item' => 'dus1', 'harga_reguler' => 5000, 'harga_classic' => 5000, 'harga_costum' => 7000],
            ['nama_item' => 'dus2', 'harga_reguler' => 10000, 'harga_classic' => 10000, 'harga_costum' => 12000],
            ['nama_item' => 'dus3', 'harga_reguler' => 15000, 'harga_classic' => 15000, 'harga_costum' => 17000],
            ['nama_item' => 'box', 'harga_reguler' => 25000, 'harga_classic' => 25000, 'harga_costum' => 28000],
            ['nama_item' => 'box12', 'harga_reguler' => 30000, 'harga_classic' => 30000, 'harga_costum' => 33000],
            ['nama_item' => 'lilin', 'harga_reguler' => 5000, 'harga_classic' => 5000, 'harga_costum' => 5000],
        ];

        foreach ($items as $item) {
            HargaItem::updateOrCreate(
                ['nama_item' => $item['nama_item']],
                ['harga_reguler' => $item['harga_reguler'], 'harga_classic' => $item['harga_classic'], 'harga_costum' => $item['harga_costum']]
            );
        }
    }
}