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
            $table->bigInteger('total_uang_penjualan')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropColumn('total_uang_penjualan');
        });
    }
};
