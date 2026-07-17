<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create transaksi_items table (gabungan dari create + 2 update)
        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->onDelete('cascade');
            $table->enum('kemasan', ['mika', 'dus1', 'dus2', 'dus3', 'box']);
            $table->integer('jumlah')->default(1);
            $table->enum('tipe', ['reguler', 'classic']);
            $table->decimal('harga', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        // Create rekaps table (gabungan dari create + add status + add used fields)
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
            $table->integer('used_mika')->default(0);
            $table->integer('used_dus1')->default(0);
            $table->integer('used_dus2')->default(0);
            $table->integer('used_dus3')->default(0);
            $table->integer('used_box')->default(0);
            $table->bigInteger('total_uang');
            $table->date('tanggal');
            $table->enum('status', ['pending', 'validated'])->default('pending');
            $table->timestamps();
        });

        // Create catatan_operasionals table (gabungan dari create + add kategori_kemasan)
        Schema::create('catatan_operasionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->foreignId('operasional_id')->constrained()->onDelete('cascade');
            $table->foreignId('pegawai_id')->constrained()->onDelete('cascade');
            $table->foreignId('rekap_id')->nullable()->constrained()->onDelete('cascade');
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->bigInteger('jumlah');
            $table->string('kategori_kemasan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        // Create histori_stoks table (gabungan dari create + add donat)
        Schema::create('histori_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_stok', ['mika', 'dus1', 'dus2', 'dus3', 'box', 'donat']);
            $table->integer('jumlah_perubahan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_stoks');
        Schema::dropIfExists('catatan_operasionals');
        Schema::dropIfExists('rekaps');
        Schema::dropIfExists('transaksi_items');
    }
};
