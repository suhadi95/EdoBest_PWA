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
            $table->integer('tambahan_gaji_1')->default(0);
            $table->integer('target_1')->default(0);
            $table->integer('tambahan_gaji_2')->default(0);
            $table->integer('target_2')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['tambahan_gaji_1', 'target_1', 'tambahan_gaji_2', 'target_2']);
        });
    }
};
