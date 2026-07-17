<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Transaksi;
use App\Models\Operasional;

class BackfillNoTransaksi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaksi:backfill-no-transaksi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill no_transaksi field for existing transactions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting backfill for no_transaksi field...');

        // Get all operasionals grouped by tanggal and outlet_id
        $operasionals = Operasional::with('transaksis')->get()->groupBy(function($item) {
            return $item->tanggal . '_' . $item->outlet_id;
        });

        $totalUpdated = 0;

        foreach ($operasionals as $groupKey => $operasionalsGroup) {
            $this->info("Processing group: {$groupKey}");
            
            foreach ($operasionalsGroup as $operasional) {
                $transaksis = $operasional->transaksis->sortBy('created_at');
                
                $counter = 1;
                foreach ($transaksis as $transaksi) {
                    if (is_null($transaksi->no_transaksi)) {
                        $transaksi->update(['no_transaksi' => $counter]);
                        $totalUpdated++;
                        $this->line("Updated transaksi ID {$transaksi->id} with no_transaksi: {$counter}");
                    }
                    $counter++;
                }
            }
        }

        $this->info("Backfill completed. Updated {$totalUpdated} transactions.");
    }
}
