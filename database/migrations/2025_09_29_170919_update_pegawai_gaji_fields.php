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
            // Add new tier salary and their conditions (syarat)
            $table->integer('syarat_tier_1')->default(0)->after('gaji_tier_1');
            $table->integer('syarat_tier_2')->default(100)->after('gaji_tier_2');
            $table->integer('syarat_tier_3')->default(150)->after('gaji_tier_3');
            $table->integer('syarat_tier_4')->default(200)->after('gaji_tier_4');
            // bonus_nominal already exists, rename bonus_syarat to bonus_syarat
            $table->renameColumn('bonus_syarat', 'bonus_syarat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn([
                'syarat_tier_1', 'syarat_tier_2', 'syarat_tier_3', 'syarat_tier_4'
            ]);
            $table->renameColumn('bonus_syarat', 'bonus_syarat');
        });
    }
};
