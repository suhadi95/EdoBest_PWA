<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->integer('no_transaksi')->nullable();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('operasional_id')->constrained('operasionals')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->enum('metode_pembayaran', ['tunai', 'qris', 'transfer', 'grabfood', 'gofood']);
            $table->integer('total_donat')->default(0);
            $table->decimal('total_harga', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('transaksi_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained('transaksis')->cascadeOnDelete();
            $table->enum('kemasan', ['mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12']);
            $table->integer('jumlah')->default(1);
            $table->enum('tipe', ['reguler', 'classic', 'custom']);
            $table->decimal('harga', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->timestamps();
        });

        Schema::create('rekaps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('operasional_id')->constrained('operasionals')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->integer('total_donat_terjual');
            $table->integer('sisa_mika');
            $table->integer('sisa_dus1');
            $table->integer('sisa_dus2');
            $table->integer('sisa_dus3');
            $table->integer('sisa_box');
            $table->integer('sisa_box12')->default(0);
            $table->integer('sisa_lilin')->default(0);
            $table->integer('used_mika')->default(0);
            $table->integer('used_dus1')->default(0);
            $table->integer('used_dus2')->default(0);
            $table->integer('used_dus3')->default(0);
            $table->integer('used_box')->default(0);
            $table->integer('used_box12')->default(0);
            $table->integer('used_lilin')->default(0);
            $table->bigInteger('total_uang');
            $table->bigInteger('total_uang_penjualan')->default(0);
            $table->bigInteger('total_tunai')->default(0);
            $table->bigInteger('total_qris')->default(0);
            $table->bigInteger('total_transfer')->default(0);
            $table->bigInteger('total_maxim')->default(0);
            $table->bigInteger('total_grabfood')->default(0);
            $table->bigInteger('total_gofood')->default(0);
            $table->bigInteger('cash_di_pegawai')->default(0);
            $table->date('tanggal');
            $table->enum('status', ['pending', 'validated'])->default('pending');
            $table->timestamps();
        });

        Schema::create('catatan_operasionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('operasional_id')->constrained('operasionals')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->foreignId('rekap_id')->nullable()->constrained('rekaps')->cascadeOnDelete();
            $table->enum('jenis', ['pemasukan', 'pengeluaran']);
            $table->bigInteger('jumlah');
            $table->string('kategori_kemasan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catatan_operasionals');
        Schema::dropIfExists('rekaps');
        Schema::dropIfExists('transaksi_items');
        Schema::dropIfExists('transaksis');
    }
};
