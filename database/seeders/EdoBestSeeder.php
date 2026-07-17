<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pegawai;
use App\Models\Outlet;

class EdoBestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data akun admin
        Pegawai::updateOrCreate(
            ['username' => 'adminku'],
            [
                'nama' => 'Admin Utama',
                'gaji_harian' => 0,
                'bonus_nominal' => 0,
                'bonus_syarat' => 0,
                'role' => 'admin',
            ]
        );

        // Data akun pegawai
        $Pegawai1 = Pegawai::updateOrCreate(
            ['username' => 'putri'],
            [
                'nama' => 'Putri',
                'gaji_harian' => 65000,
                'tambahan_gaji_1' => 5000,
                'target_1' => 150,
                'tambahan_gaji_2' => 10000,
                'target_2' => 200,
                'tambahan_gaji_3' => 10000,
                'target_3' => 250,
                'tambahan_gaji_4' => 10000,
                'target_4' => 300,
                'bonus_nominal' => 5000,
                'bonus_syarat' => 100,
                'role' => 'pegawai',
            ]
        );

        $outlet1 = Outlet::updateOrCreate(
            ['nama' => 'E-Donuts Toha'],
            [
                'alamat' => 'Toha',
            ]
        );

        // Assign pegawai to outlets
        $Pegawai1->outlet_id = $outlet1->id;
        $Pegawai1->save();
    }
}
