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
            $table->integer('tambahan_gaji_3')->default(0)->after('tambahan_gaji_2');
            $table->integer('target_3')->default(0)->after('target_2');
            $table->integer('tambahan_gaji_4')->default(0)->after('tambahan_gaji_3');
            $table->integer('target_4')->default(0)->after('target_3');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropColumn(['tambahan_gaji_3', 'target_3', 'tambahan_gaji_4', 'target_4']);
        });
    }
};
