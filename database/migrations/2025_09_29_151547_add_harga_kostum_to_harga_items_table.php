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
        Schema::table('harga_items', function (Blueprint $table) {
            $table->decimal('harga_costum', 10, 2)->default(0)->after('harga_classic');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('harga_items', function (Blueprint $table) {
            $table->dropColumn('harga_costum');
        });
    }
};
