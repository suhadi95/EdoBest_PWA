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
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['aktif', 'tutup', 'selesai'])->default('tutup');
            $table->integer('total_donat_harian')->default(0);
            $table->unsignedInteger('biaya_listrik')->default(0);
            $table->unsignedBigInteger('listrik_pembayaran_id')->nullable();
            $table->timestamps();
        });

        Schema::create('stok_outlets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->integer('stok_mika')->default(0);
            $table->integer('stok_dus1')->default(0);
            $table->integer('stok_dus2')->default(0);
            $table->integer('stok_dus3')->default(0);
            $table->integer('stok_box')->default(0);
            $table->integer('stok_box12')->default(0);
            $table->integer('stok_lilin')->default(0);
            $table->timestamps();
        });

        Schema::create('kloters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operasional_id')->constrained('operasionals')->cascadeOnDelete();
            $table->integer('jumlah_donat')->default(0);
            $table->integer('jumlah_mika')->default(0);
            $table->integer('jumlah_dus1')->default(0);
            $table->integer('jumlah_dus2')->default(0);
            $table->integer('jumlah_dus3')->default(0);
            $table->integer('jumlah_box')->default(0);
            $table->integer('jumlah_box12')->default(0);
            $table->integer('jumlah_lilin')->default(0);
            $table->timestamps();
        });

        Schema::create('harga_items', function (Blueprint $table) {
            $table->id();
            $table->enum('nama_item', ['mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12']);
            $table->decimal('harga_reguler', 10, 2)->default(0);
            $table->decimal('harga_classic', 10, 2)->default(0);
            $table->decimal('harga_costum', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('histori_stoks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->enum('jenis_stok', ['mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12']);
            $table->integer('jumlah_perubahan');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('histori_stoks');
        Schema::dropIfExists('harga_items');
        Schema::dropIfExists('kloters');
        Schema::dropIfExists('stok_outlets');
        Schema::dropIfExists('operasionals');
    }
};
