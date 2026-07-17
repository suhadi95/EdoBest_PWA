<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\HargaItem;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add box12 to enums
        DB::statement("ALTER TABLE harga_items MODIFY COLUMN nama_item ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12')");
        DB::statement("ALTER TABLE transaksi_items MODIFY COLUMN kemasan ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12')");
        DB::statement("ALTER TABLE histori_stoks MODIFY COLUMN jenis_stok ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12')");

        // Add columns
        if (!Schema::hasColumn('stok_outlets', 'stok_box12')) {
            Schema::table('stok_outlets', function (Blueprint $table) {
                $table->integer('stok_box12')->default(0)->after('stok_box');
            });
        }

        if (!Schema::hasColumn('rekaps', 'sisa_box12')) {
            Schema::table('rekaps', function (Blueprint $table) {
                $table->integer('sisa_box12')->default(0)->after('sisa_box');
                $table->integer('used_box12')->default(0)->after('used_box');
            });
        }

        if (!Schema::hasColumn('kloters', 'jumlah_box12')) {
            Schema::table('kloters', function (Blueprint $table) {
                $table->integer('jumlah_box12')->default(0)->after('jumlah_box');
            });
        }

        // Insert price
        if (DB::table('harga_items')->where('nama_item', 'box12')->doesntExist()) {
            DB::table('harga_items')->insert([
                'nama_item' => 'box12',
                'harga_reguler' => 50000,
                'harga_classic' => 50000,
                'harga_costum' => 53000,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Delete price
        DB::table('harga_items')->where('nama_item', 'box12')->delete();

        Schema::table('kloters', function (Blueprint $table) {
            $table->dropColumn('jumlah_box12');
        });

        Schema::table('rekaps', function (Blueprint $table) {
            $table->dropColumn(['sisa_box12', 'used_box12']);
        });

        Schema::table('stok_outlets', function (Blueprint $table) {
            $table->dropColumn('stok_box12');
        });

        // Revert enums
        DB::statement("ALTER TABLE harga_items MODIFY COLUMN nama_item ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin')");
        DB::statement("ALTER TABLE transaksi_items MODIFY COLUMN kemasan ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin')");
        DB::statement("ALTER TABLE histori_stoks MODIFY COLUMN jenis_stok ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin')");
    }
};
