<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('histori_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->enum('jenis_stok', ['mika', 'dus1', 'dus2', 'dus3', 'box']);
            $table->integer('jumlah_perubahan'); // Positif untuk tambah, negatif untuk kurang
            $table->string('keterangan'); // Misalnya: 'Dari Kloter', 'Tambah Manual', 'Kurang Manual'
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_stoks');
    }
};