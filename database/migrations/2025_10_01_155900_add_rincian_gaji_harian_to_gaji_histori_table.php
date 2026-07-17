<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('gaji_histori', function (Blueprint $table) {
            $table->json('rincian_gaji_harian')->nullable()->after('gaji_bersih');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_histori', function (Blueprint $table) {
            $table->dropColumn('rincian_gaji_harian');
        });
    }
};
