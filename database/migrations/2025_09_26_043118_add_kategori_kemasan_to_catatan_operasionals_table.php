<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('catatan_operasionals', function (Blueprint $table) {
            $table->string('kategori_kemasan')->nullable()->after('jumlah'); // Kolom untuk jenis kemasan (mika, dus1, dll.)
        });
    }

    public function down(): void
    {
        Schema::table('catatan_operasionals', function (Blueprint $table) {
            $table->dropColumn('kategori_kemasan');
        });
    }
};