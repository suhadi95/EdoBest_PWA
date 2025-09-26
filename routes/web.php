<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\OperasionalController;
use App\Http\Controllers\Admin\StokKemasanController;
use App\Http\Controllers\Admin\HargaItemController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Admin\RekapController;

// Route untuk home (redirect ke login jika belum login)
Route::get('/', function () {
    if (session('user')) {
        if (session('user')->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif (session('user')->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }
    }
    return redirect()->route('login');
})->name('home');

// Route untuk login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route untuk Admin
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    // Kelola Pegawai
    Route::get('/kelola-pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
    Route::post('/kelola-pegawai', [PegawaiController::class, 'store'])->name('pegawai.store');
    Route::get('/kelola-pegawai/{id}/edit', [PegawaiController::class, 'edit'])->name('pegawai.edit');
    Route::put('/kelola-pegawai/{id}', [PegawaiController::class, 'update'])->name('pegawai.update');
    Route::delete('/kelola-pegawai/{id}', [PegawaiController::class, 'destroy'])->name('pegawai.destroy');

    // Kelola Outlet
    Route::get('/kelola-outlet', [OutletController::class, 'index'])->name('outlet.index');
    Route::post('/kelola-outlet', [OutletController::class, 'store'])->name('outlet.store');
    Route::get('/kelola-outlet/{id}/edit', [OutletController::class, 'edit'])->name('outlet.edit');
    Route::put('/kelola-outlet/{id}', [OutletController::class, 'update'])->name('outlet.update');
    Route::delete('/kelola-outlet/{id}', [OutletController::class, 'destroy'])->name('outlet.destroy');

    // Operasional Harian
    Route::get('/operasional', [OperasionalController::class, 'index'])->name('operasional.index');
    Route::post('/mulai-operasional', [OperasionalController::class, 'mulaiOperasional'])->name('operasional.mulai');
    Route::get('/operasional/{outlet_id}', [OperasionalController::class, 'detail'])->name('operasional.detail');
    Route::post('/tambah-kloter', [OperasionalController::class, 'tambahKloter'])->name('operasional.kloter.store');
    Route::post('/update-stok', [OperasionalController::class, 'updateStok'])->name('operasional.stok.update');

    // Kelola Stok Kemasan
    Route::get('/kelola-stok-kemasan', [StokKemasanController::class, 'index'])->name('stok.index');
    Route::get('/detail-stok/{outlet_id}', [StokKemasanController::class, 'detail'])->name('stok.detail');
    Route::post('/update-stok', [StokKemasanController::class, 'updateStok'])->name('stok.update');

    // Kelola Harga Item
    Route::get('/kelola-harga', [HargaItemController::class, 'index'])->name('harga.index');
    Route::get('/kelola-harga/{id}/edit', [HargaItemController::class, 'edit'])->name('harga.edit');
    Route::put('/kelola-harga/{id}', [HargaItemController::class, 'update'])->name('harga.update');

    // Laporan Rekap
    Route::get('/rekap', [RekapController::class, 'index'])->name('admin.rekap.index');
    Route::get('/rekap/{outlet_id}', [RekapController::class, 'detail'])->name('admin.rekap.detail');
    Route::post('/rekap/{outlet_id}/{rekap_id}/validasi', [RekapController::class, 'validasiRekap'])->name('admin.rekap.validasi');
});

// Route untuk Pegawai
Route::prefix('pegawai')->middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('pegawai.dashboard');
    Route::get('/penjualan/{outlet_id}', [PegawaiDashboardController::class, 'penjualan'])->name('pegawai.penjualan');
    Route::get('/transaksi/{outlet_id}', [PegawaiDashboardController::class, 'transaksi'])->name('pegawai.transaksi');
    Route::post('/tambah-item', [PegawaiDashboardController::class, 'tambahItem'])->name('pegawai.tambah-item');
    Route::post('/hapus-item', [PegawaiDashboardController::class, 'hapusItem'])->name('pegawai.hapus-item');
    Route::post('/simpan-transaksi', [PegawaiDashboardController::class, 'simpanTransaksi'])->name('pegawai.simpan-transaksi');
    Route::get('/transaksi/{outlet_id}/{transaksi_id}', [PegawaiDashboardController::class, 'detailTransaksi'])->name('pegawai.transaksi.detail');
    Route::get('/rekap/{outlet_id}', [PegawaiDashboardController::class, 'rekap'])->name('pegawai.rekap');
    Route::post('/tambah-catatan', [PegawaiDashboardController::class, 'tambahCatatan'])->name('pegawai.tambah-catatan');
    Route::post('/hapus-catatan', [PegawaiDashboardController::class, 'hapusCatatan'])->name('pegawai.hapus-catatan');
    Route::post('/simpan-rekap', [PegawaiDashboardController::class, 'simpanRekap'])->name('pegawai.simpan-rekap');
    Route::get('/rekap/{outlet_id}/{rekap_id}', [PegawaiDashboardController::class, 'rekapDetail'])->name('pegawai.rekap.detail');
});
