<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stok_outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->onDelete('cascade');
            $table->integer('stok_mika')->default(0);
            $table->integer('stok_dus1')->default(0);
            $table->integer('stok_dus2')->default(0);
            $table->integer('stok_dus3')->default(0);
            $table->integer('stok_box')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stok_outlets');
    }
};