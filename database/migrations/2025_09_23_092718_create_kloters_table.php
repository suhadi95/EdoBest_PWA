<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kloters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operasional_id')->constrained('operasionals')->onDelete('cascade');
            $table->integer('jumlah_donat')->default(0);
            $table->integer('jumlah_mika')->default(0);
            $table->integer('jumlah_dus1')->default(0);
            $table->integer('jumlah_dus2')->default(0);
            $table->integer('jumlah_dus3')->default(0);
            $table->integer('jumlah_box')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kloters');
    }
};