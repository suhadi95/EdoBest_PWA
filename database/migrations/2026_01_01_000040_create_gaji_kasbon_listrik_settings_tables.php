<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gaji_histori', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->date('tanggal_gaji');
            $table->date('periode_mulai')->nullable();
            $table->date('periode_akhir')->nullable();
            $table->string('periode_keterangan')->nullable();
            $table->integer('gaji_harian')->default(0);
            $table->integer('kasbon')->default(0);
            $table->integer('gaji_bersih')->default(0);
            $table->json('rincian_gaji_harian')->nullable();
            $table->integer('gaji_total')->default(0);
            $table->enum('status', ['paid', 'unpaid'])->default('unpaid');
            $table->timestamps();
        });

        Schema::create('catatan_gaji', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gaji_histori_id')->nullable()->constrained('gaji_histori')->cascadeOnDelete();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->enum('jenis', ['tambahan', 'pengurangan']);
            $table->bigInteger('jumlah');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('kasbons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->cascadeOnDelete();
            $table->date('tanggal');
            $table->integer('nominal');
            $table->text('keterangan');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('status_pembayaran', ['belum_dibayar', 'lunas'])->default('belum_dibayar');
            $table->timestamps();
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
            $table->foreign('listrik_pembayaran_id')
                ->references('id')
                ->on('listrik_pembayarans')
                ->nullOnDelete();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        $now = now();
        DB::table('app_settings')->insert([
            [
                'key' => 'aipos_url',
                'value' => 'https://www.aiposystem.com/my/dashboard',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'aipos_email',
                'value' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'key' => 'aipos_password',
                'value' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');

        Schema::table('operasionals', function (Blueprint $table) {
            $table->dropForeign(['listrik_pembayaran_id']);
        });

        Schema::dropIfExists('listrik_pembayarans');
        Schema::dropIfExists('kasbons');
        Schema::dropIfExists('catatan_gaji');
        Schema::dropIfExists('gaji_histori');
    }
};
