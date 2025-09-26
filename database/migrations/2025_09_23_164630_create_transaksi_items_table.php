<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade'); // Diperbaiki ke transaksis
            $table->enum('kemasan', ['mika', 'dus1', 'dus2', 'dus3', 'box']);
            $table->integer('jumlah')->default(1);
            $table->enum('tipe', ['original', 'klasik']);
            $table->integer('donat_per_item');
            $table->decimal('total_harga', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaksi_items');
    }
};