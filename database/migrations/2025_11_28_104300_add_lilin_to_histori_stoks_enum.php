<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE histori_stoks MODIFY COLUMN jenis_stok ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'donat', 'lilin')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE histori_stoks MODIFY COLUMN jenis_stok ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'donat')");
    }
};
