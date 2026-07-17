<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operasionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->date('tanggal'); // Hari operasional
            $table->enum('status', ['aktif', 'tutup', 'selesai'])->default('tutup');
            $table->integer('total_donat_harian')->default(0); // Total donat dari kloter hari itu
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operasionals');
    }
};