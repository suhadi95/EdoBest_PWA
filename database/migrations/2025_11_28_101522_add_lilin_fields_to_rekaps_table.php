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
            $table->integer('sisa_lilin')->default(0)->after('sisa_box');
            $table->integer('used_lilin')->default(0)->after('used_box');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropColumn(['sisa_lilin', 'used_lilin']);
        });
    }
};
