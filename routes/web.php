<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\RekapController as AdminRekapController;
use App\Http\Controllers\Admin\OutletController;
use App\Http\Controllers\Admin\PegawaiController;
use App\Http\Controllers\Admin\HargaItemController;
use App\Http\Controllers\Admin\OperasionalController;
use App\Http\Controllers\Admin\StokKemasanController;
use App\Http\Controllers\Pegawai\PegawaiDashboardController;
use App\Http\Controllers\Pegawai\PenjualanController;
use App\Http\Controllers\Pegawai\RekapController;
use App\Http\Controllers\Admin\PenggajianController;
use App\Http\Controllers\Admin\KasbonController as AdminKasbonController;
use App\Http\Controllers\Admin\AiposSettingController;
use App\Http\Controllers\Pegawai\KasbonController as PegawaiKasbonController;
use App\Http\Controllers\Pegawai\ListrikController;
use App\Http\Controllers\Pegawai\AiposController;

// Route untuk login
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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
    Route::delete('/hapus-kloter/{kloter_id}', [OperasionalController::class, 'hapusKloter'])->name('operasional.kloter.delete');
    Route::post('/update-stok', [OperasionalController::class, 'updateStok'])->name('operasional.stok.update');
    Route::post('/validasi-operasional', [OperasionalController::class, 'validasiOperasional'])->name('operasional.validasi');

    // Kelola Stok Kemasan
    Route::get('/kelola-stok-kemasan', [StokKemasanController::class, 'index'])->name('stok.index');
    Route::get('/detail-stok/{outlet_id}', [StokKemasanController::class, 'detail'])->name('stok.detail');
    Route::post('/update-stok', [StokKemasanController::class, 'updateStok'])->name('stok.update');

    // Kelola Harga Item
    Route::get('/kelola-harga', [HargaItemController::class, 'index'])->name('harga.index');
    Route::get('/kelola-harga/{id}/edit', [HargaItemController::class, 'edit'])->name('harga.edit');
    Route::put('/kelola-harga/{id}', [HargaItemController::class, 'update'])->name('harga.update');

    // Laporan Rekap
    Route::get('/rekap', [AdminRekapController::class, 'index'])->name('admin.rekap.index');
    Route::get('/rekap/{outlet_id}', [AdminRekapController::class, 'detail'])->name('admin.rekap.detail');
    Route::post('/rekap/{outlet_id}/{rekap_id}/validasi', [AdminRekapController::class, 'validasiRekap'])->name('admin.rekap.validasi');

    // Penggajian
    Route::get('/penggajian', [PenggajianController::class, 'index'])->name('penggajian.index');
    Route::get('/penggajian/{pegawaiId}', [PenggajianController::class, 'show'])->name('penggajian.show');
    Route::get('/penggajian/{pegawaiId}/detail/{gajiId}', [PenggajianController::class, 'detailGaji'])->name('penggajian.detail');
    Route::post('/penggajian/{pegawaiId}/calculate', [PenggajianController::class, 'calculateGajiAjax'])->name('penggajian.calculate');
    Route::post('/penggajian/{pegawaiId}/validate', [PenggajianController::class, 'validateGaji'])->name('penggajian.validate');
    Route::post('/penggajian/{pegawaiId}/tambah-catatan-gaji', [PenggajianController::class, 'tambahCatatanGaji'])->name('penggajian.tambah-catatan-gaji');
    Route::post('/penggajian/{pegawaiId}/hapus-catatan-gaji', [PenggajianController::class, 'hapusCatatanGaji'])->name('penggajian.hapus-catatan-gaji');

    // Kasbon
    Route::get('/kasbon', [AdminKasbonController::class, 'index'])->name('admin.kasbon.index');
    Route::get('/kasbon/{pegawaiId}', [AdminKasbonController::class, 'showPegawaiKasbon'])->name('admin.kasbon.show');
    Route::post('/kasbon/{id}/approve', [AdminKasbonController::class, 'approveKasbon'])->name('admin.kasbon.approve');
    Route::post('/kasbon/{id}/reject', [AdminKasbonController::class, 'rejectKasbon'])->name('admin.kasbon.reject');

    // Pengaturan AIPOS
    Route::get('/aipos', [AiposSettingController::class, 'edit'])->name('admin.aipos.edit');
    Route::put('/aipos', [AiposSettingController::class, 'update'])->name('admin.aipos.update');
});

use App\Http\Controllers\Pegawai\HistoriGajiController;
use App\Http\Controllers\Pegawai\HistoriRekapController;

Route::prefix('pegawai')->middleware(['auth', 'role:pegawai'])->group(function () {
    Route::get('/dashboard', [PegawaiDashboardController::class, 'index'])->name('pegawai.dashboard');
    Route::get('/penjualan/{outlet_id}', [PenjualanController::class, 'penjualan'])->name('pegawai.penjualan');
    Route::get('/transaksi/{outlet_id}', [PenjualanController::class, 'transaksi'])->name('pegawai.transaksi');
    Route::post('/tambah-item', [PenjualanController::class, 'tambahItem'])->name('pegawai.tambah-item');
    Route::post('/hapus-item', [PenjualanController::class, 'hapusItem'])->name('pegawai.hapus-item');
    Route::post('/hapus-semua-item', [PenjualanController::class, 'hapusSemuaItem'])->name('pegawai.hapus-semua-item');
    Route::post('/simpan-transaksi', [PenjualanController::class, 'simpanTransaksi'])->name('pegawai.simpan-transaksi');
    Route::get('/transaksi/{outlet_id}/{transaksi_id}', [PenjualanController::class, 'detailTransaksi'])->name('pegawai.transaksi.detail');
    Route::delete('/transaksi/{outlet_id}/{transaksi_id}', [PenjualanController::class, 'deleteTransaksi'])->name('pegawai.transaksi.delete');
    Route::get('/pilih-tanggal-rekap/{outlet_id}', [RekapController::class, 'pilihTanggal'])->name('pegawai.pilih-tanggal-rekap');
    Route::get('/rekap/{outlet_id}', [RekapController::class, 'rekap'])->name('pegawai.rekap');
    Route::post('/tambah-catatan', [RekapController::class, 'tambahCatatan'])->name('pegawai.tambah-catatan');
    Route::post('/hapus-catatan', [RekapController::class, 'hapusCatatan'])->name('pegawai.hapus-catatan');
    Route::post('/simpan-rekap', [RekapController::class, 'simpanRekap'])->name('pegawai.simpan-rekap');
    Route::get('/rekap/{outlet_id}/{rekap_id}', [RekapController::class, 'rekapDetail'])->name('pegawai.rekap.detail');
    Route::delete('/rekap/{outlet_id}/{rekap_id}', [RekapController::class, 'destroyRekap'])->name('pegawai.rekap.delete');

    // Histori Gaji
    Route::get('/histori-gaji', [HistoriGajiController::class, 'index'])->name('pegawai.histori-gaji.index');
    Route::get('/histori-gaji/detail/{gajiId}', [HistoriGajiController::class, 'detail'])->name('pegawai.histori-gaji.detail');

    // Histori Rekap
    Route::get('/histori-rekap', [HistoriRekapController::class, 'index'])->name('pegawai.histori-rekap.index');
    Route::get('/histori-rekap/detail/{rekap_id}', [HistoriRekapController::class, 'detail'])->name('pegawai.histori-rekap.detail');

    // Kasbon
    Route::get('/kasbon', [PegawaiKasbonController::class, 'index'])->name('pegawai.kasbon.index');
    Route::post('/kasbon', [PegawaiKasbonController::class, 'store'])->name('pegawai.kasbon.store');
    Route::delete('/kasbon/{id}', [PegawaiKasbonController::class, 'destroy'])->name('pegawai.kasbon.destroy');

    // Catatan Listrik
    Route::get('/listrik', [ListrikController::class, 'index'])->name('pegawai.listrik.index');
    Route::post('/listrik/bayar', [ListrikController::class, 'bayar'])->name('pegawai.listrik.bayar');

    // Akses AIPOS
    Route::get('/aipos', [AiposController::class, 'index'])->name('pegawai.aipos.index');
});
