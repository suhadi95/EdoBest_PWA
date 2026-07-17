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
        Schema::table('pegawais', function (Blueprint $table) {
            $table->integer('gaji_tier_1')->default(65000)->after('gaji_harian');
            $table->integer('gaji_tier_2')->default(70000)->after('gaji_tier_1');
            $table->integer('gaji_tier_3')->default(80000)->after('gaji_tier_2');
            $table->integer('gaji_tier_4')->default(90000)->after('gaji_tier_3');
            $table->integer('gaji_tier_5')->default(100000)->after('gaji_tier_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['gaji_tier_1', 'gaji_tier_2', 'gaji_tier_3', 'gaji_tier_4', 'gaji_tier_5']);
        });
    }
};
