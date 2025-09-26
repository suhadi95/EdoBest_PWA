<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\HargaItem;

class HargaItemSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            ['nama_item' => 'mika', 'harga_original' => 5000, 'harga_klasik' => 4000],
            ['nama_item' => 'dus1', 'harga_original' => 5000, 'harga_klasik' => 4000],
            ['nama_item' => 'dus2', 'harga_original' => 10000, 'harga_klasik' => 8000],
            ['nama_item' => 'dus3', 'harga_original' => 15000, 'harga_klasik' => 12000],
            ['nama_item' => 'box', 'harga_original' => 25000, 'harga_klasik' => 20000],
        ];

        foreach ($items as $item) {
            HargaItem::updateOrCreate(
                ['nama_item' => $item['nama_item']],
                ['harga_original' => $item['harga_original'], 'harga_klasik' => $item['harga_klasik']]
            );
        }
    }
}