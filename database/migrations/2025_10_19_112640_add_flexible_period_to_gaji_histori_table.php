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
            $table->date('periode_mulai')->nullable()->after('tanggal_gaji');
            $table->date('periode_akhir')->nullable()->after('periode_mulai');
            $table->string('periode_keterangan')->nullable()->after('periode_akhir');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gaji_histori', function (Blueprint $table) {
            $table->dropColumn(['periode_mulai', 'periode_akhir', 'periode_keterangan']);
        });
    }
};
