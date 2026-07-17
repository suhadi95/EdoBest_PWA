<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

try {
    echo "Altering harga_items...\n";
    DB::statement("ALTER TABLE harga_items MODIFY COLUMN nama_item ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12')");
    echo "Success harga_items.\n";
} catch (\Exception $e) {
    echo "Error harga_items: " . $e->getMessage() . "\n";
}

try {
    echo "Altering histori_stoks...\n";
    DB::statement("ALTER TABLE histori_stoks MODIFY COLUMN jenis_stok ENUM('mika', 'dus1', 'dus2', 'dus3', 'box', 'lilin', 'box12')");
    echo "Success histori_stoks.\n";
} catch (\Exception $e) {
    echo "Error histori_stoks: " . $e->getMessage() . "\n";
}

try {
    echo "Adding stok_box12 to stok_outlets...\n";
    Schema::table('stok_outlets', function ($table) {
        $table->integer('stok_box12')->default(0)->after('stok_box');
    });
    echo "Success stok_outlets.\n";
} catch (\Exception $e) {
    echo "Error stok_outlets: " . $e->getMessage() . "\n";
}

try {
    echo "Adding sisa_box12 to rekaps...\n";
    Schema::table('rekaps', function ($table) {
        $table->integer('sisa_box12')->default(0)->after('sisa_box');
        $table->integer('used_box12')->default(0)->after('used_box');
    });
    echo "Success rekaps.\n";
} catch (\Exception $e) {
    echo "Error rekaps: " . $e->getMessage() . "\n";
}

try {
    echo "Adding jumlah_box12 to kloters...\n";
    Schema::table('kloters', function ($table) {
        $table->integer('jumlah_box12')->default(0)->after('jumlah_box');
    });
    echo "Success kloters.\n";
} catch (\Exception $e) {
    echo "Error kloters: " . $e->getMessage() . "\n";
}

try {
    echo "Inserting price...\n";
    DB::table('harga_items')->insert([
        'nama_item' => 'box12',
        'harga_reguler' => 50000,
        'harga_classic' => 50000,
        'harga_costum' => 53000,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    echo "Success insert price.\n";
} catch (\Exception $e) {
    echo "Error insert price: " . $e->getMessage() . "\n";
}
