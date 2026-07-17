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
            $table->bigInteger('total_tunai')->default(0)->after('total_uang_penjualan');
            $table->bigInteger('total_qris')->default(0)->after('total_tunai');
            $table->bigInteger('total_transfer')->default(0)->after('total_qris');
            $table->bigInteger('total_maxim')->default(0)->after('total_transfer');
            $table->bigInteger('total_grabfood')->default(0)->after('total_maxim');
            $table->bigInteger('total_gofood')->default(0)->after('total_grabfood');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropColumn(['total_tunai', 'total_qris', 'total_transfer', 'total_maxim', 'total_grabfood', 'total_gofood']);
        });
    }
};
