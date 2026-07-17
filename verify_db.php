<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

echo "Checking stok_outlets...\n";
if (Schema::hasColumn('stok_outlets', 'stok_box12')) {
    echo "stok_box12 exists.\n";
} else {
    echo "stok_box12 MISSING.\n";
}

echo "Checking rekaps...\n";
if (Schema::hasColumn('rekaps', 'sisa_box12')) {
    echo "sisa_box12 exists.\n";
} else {
    echo "sisa_box12 MISSING.\n";
}

echo "Checking kloters...\n";
if (Schema::hasColumn('kloters', 'jumlah_box12')) {
    echo "jumlah_box12 exists.\n";
} else {
    echo "jumlah_box12 MISSING.\n";
}

echo "Checking harga_items...\n";
$price = DB::table('harga_items')->where('nama_item', 'box12')->first();
if ($price) {
    echo "Price box12 found: " . json_encode($price) . "\n";
} else {
    echo "Price box12 NOT FOUND.\n";
}

try {
    $type = DB::select("SHOW COLUMNS FROM harga_items WHERE Field = 'nama_item'")[0]->Type;
    echo "harga_items enum: $type\n";
} catch (\Exception $e) {
    echo "Error checking enum: " . $e->getMessage() . "\n";
}
