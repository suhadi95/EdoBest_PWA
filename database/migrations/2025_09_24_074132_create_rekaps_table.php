<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rekaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('operasional_id')->constrained()->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->integer('total_donat_terjual');
            $table->integer('sisa_mika');
            $table->integer('sisa_dus1');
            $table->integer('sisa_dus2');
            $table->integer('sisa_dus3');
            $table->integer('sisa_box');
            $table->bigInteger('total_uang');
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rekaps');
    }
};