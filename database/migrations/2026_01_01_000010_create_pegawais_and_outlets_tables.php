<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('username')->unique();
            $table->integer('gaji_harian')->default(0);
            $table->integer('gaji_tier_1')->default(65000);
            $table->integer('syarat_tier_1')->default(0);
            $table->integer('gaji_tier_2')->default(70000);
            $table->integer('syarat_tier_2')->default(100);
            $table->integer('gaji_tier_3')->default(80000);
            $table->integer('syarat_tier_3')->default(150);
            $table->integer('gaji_tier_4')->default(90000);
            $table->integer('syarat_tier_4')->default(200);
            $table->integer('gaji_tier_5')->default(100000);
            $table->integer('bonus_nominal')->default(0);
            $table->integer('bonus_syarat')->default(0);
            $table->string('role');
            $table->integer('tambahan_gaji_1')->default(0);
            $table->integer('target_1')->default(0);
            $table->integer('tambahan_gaji_2')->default(0);
            $table->integer('target_2')->default(0);
            $table->integer('tambahan_gaji_3')->default(0);
            $table->integer('target_3')->default(0);
            $table->integer('tambahan_gaji_4')->default(0);
            $table->integer('target_4')->default(0);
            $table->unsignedBigInteger('outlet_id')->nullable();
            $table->timestamps();
        });

        Schema::create('outlets', function (Blueprint $table) {
            $table->id();
            $table->string('nama')->unique();
            $table->text('alamat');
            $table->unsignedInteger('biaya_listrik_harian')->default(0);
            $table->timestamps();
        });

        Schema::table('pegawais', function (Blueprint $table) {
            $table->foreign('outlet_id')->references('id')->on('outlets')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pegawais', function (Blueprint $table) {
            $table->dropForeign(['outlet_id']);
        });
        Schema::dropIfExists('outlets');
        Schema::dropIfExists('pegawais');
    }
};
