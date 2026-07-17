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
        Schema::table('stok_outlets', function (Blueprint $table) {
            $table->integer('stok_lilin')->default(0)->after('stok_box');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stok_outlets', function (Blueprint $table) {
            $table->dropColumn('stok_lilin');
        });
    }
};
