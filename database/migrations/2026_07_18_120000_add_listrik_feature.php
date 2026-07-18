<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('outlets', function (Blueprint $table) {
            $table->unsignedInteger('biaya_listrik_harian')->default(0)->after('alamat');
        });

        Schema::create('listrik_pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outlet_id')->constrained('outlets')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->nullable()->constrained('pegawais')->nullOnDelete();
            $table->unsignedInteger('jumlah_hari');
            $table->unsignedInteger('total_nominal');
            $table->timestamp('dibayar_at');
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::table('operasionals', function (Blueprint $table) {
            $table->unsignedInteger('biaya_listrik')->default(0)->after('total_donat_harian');
            $table->foreignId('listrik_pembayaran_id')
                ->nullable()
                ->after('biaya_listrik')
                ->constrained('listrik_pembayarans')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('operasionals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('listrik_pembayaran_id');
            $table->dropColumn('biaya_listrik');
        });

        Schema::dropIfExists('listrik_pembayarans');

        Schema::table('outlets', function (Blueprint $table) {
            $table->dropColumn('biaya_listrik_harian');
        });
    }
};
