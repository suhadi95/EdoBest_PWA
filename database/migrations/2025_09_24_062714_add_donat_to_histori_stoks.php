<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('histori_stoks', function (Blueprint $table) {
            $table->string('jenis_stok')->change();
        });
    }

    public function down(): void
    {
        Schema::table('histori_stoks', function (Blueprint $table) {
            $table->enum('jenis_stok', ['mika', 'dus1', 'dus2', 'dus3', 'box'])->change();
        });
    }
};