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
        Schema::table('rekaps', function (Blueprint $table) {
            $table->bigInteger('cash_di_pegawai')->default(0)->after('total_gofood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropColumn('cash_di_pegawai');
        });
    }
};


