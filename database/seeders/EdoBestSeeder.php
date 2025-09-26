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
        Pegawai::create([
            'nama' => 'Admin Utama',
            'username' => 'admin',
            'gaji_harian' => 0,
            'bonus_nominal' => 0,
            'bonus_syarat' => 0,
            'role' => 'admin',
        ]);

        // Data akun pegawai
        $Pegawai1 = Pegawai::create([
            'nama' => 'Danu',
            'username' => 'danu',
            'gaji_harian' => 100000,
            'bonus_nominal' => 50000,
            'bonus_syarat' => 50,
            'role' => 'pegawai',
        ]);

        $Pegawai2 = Pegawai::create([
            'nama' => 'Putri',
            'username' => 'putri',
            'gaji_harian' => 100000,
            'bonus_nominal' => 50000,
            'bonus_syarat' => 50,
            'role' => 'pegawai',
        ]);

        Outlet::create([
            'nama' => 'E-Donuts Toha',
            'alamat' => 'Toha',
            'pegawai_id' => $Pegawai1->id,
        ]);
    }
}