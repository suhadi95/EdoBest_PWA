<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rekap;
use App\Models\Transaksi;
use App\Models\TransaksiItem;

class BackfillUsedKemasan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rekap:backfill-used-kemasan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill used kemasan fields in rekaps table based on transaksi items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill for used kemasan fields...');

        $rekaps = Rekap::all();

        foreach ($rekaps as $rekap) {
            $transaksis = Transaksi::where('operasional_id', $rekap->operasional_id)->with('items')->get();

            $usedMika = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'mika')->sum('jumlah');
            });
            $usedDus1 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'dus1')->sum('jumlah');
            });
            $usedDus2 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'dus2')->sum('jumlah');
            });
            $usedDus3 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'dus3')->sum('jumlah');
            });
            $usedBox = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'box')->sum('jumlah');
            });

            // Hitung total kemasan dari kloters
            $kloters = Kloter::where('operasional_id', $rekap->operasional_id)->get();
            $totalMika = $kloters->sum('jumlah_mika');
            $totalDus1 = $kloters->sum('jumlah_dus1');
            $totalDus2 = $kloters->sum('jumlah_dus2');
            $totalDus3 = $kloters->sum('jumlah_dus3');
            $totalBox = $kloters->sum('jumlah_box');

            $sisaMika = $totalMika - $usedMika;
            $sisaDus1 = $totalDus1 - $usedDus1;
            $sisaDus2 = $totalDus2 - $usedDus2;
            $sisaDus3 = $totalDus3 - $usedDus3;
            $sisaBox = $totalBox - $usedBox;

            $rekap->update([
                'used_mika' => $usedMika,
                'used_dus1' => $usedDus1,
                'used_dus2' => $usedDus2,
                'used_dus3' => $usedDus3,
                'used_box' => $usedBox,
                'sisa_mika' => $sisaMika,
                'sisa_dus1' => $sisaDus1,
                'sisa_dus2' => $sisaDus2,
                'sisa_dus3' => $sisaDus3,
                'sisa_box' => $sisaBox,
            ]);

            $this->info("Updated rekap ID {$rekap->id}: used_mika={$usedMika}, sisa_mika={$sisaMika}, used_dus1={$usedDus1}, sisa_dus1={$sisaDus1}, used_dus2={$usedDus2}, sisa_dus2={$sisaDus2}, used_dus3={$usedDus3}, sisa_dus3={$sisaDus3}, used_box={$usedBox}, sisa_box={$sisaBox}");
        }

        $this->info('Backfill completed successfully.');
    }
}
