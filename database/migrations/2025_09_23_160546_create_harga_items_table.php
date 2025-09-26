<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harga_items', function (Blueprint $table) {
            $table->id();
            $table->enum('nama_item', ['mika', 'dus1', 'dus2', 'dus3', 'box']);
            $table->decimal('harga_original', 10, 2)->default(0);
            $table->decimal('harga_klasik', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harga_items');
    }
};