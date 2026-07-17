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
            $table->integer('gaji_harian')->after('tanggal_gaji');
            $table->integer('kasbon')->after('gaji_harian');
            $table->integer('gaji_bersih')->after('kasbon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_histori', function (Blueprint $table) {
            $table->dropColumn(['gaji_harian', 'kasbon', 'gaji_bersih']);
        });
    }
};
