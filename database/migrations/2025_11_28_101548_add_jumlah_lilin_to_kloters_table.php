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
        Schema::table('kloters', function (Blueprint $table) {
            $table->integer('jumlah_lilin')->default(0)->after('jumlah_box');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kloters', function (Blueprint $table) {
            $table->dropColumn('jumlah_lilin');
        });
    }
};
