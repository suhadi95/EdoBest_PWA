<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Operasional;
use App\Models\Transaksi;
use App\Models\StokOutlet;
use App\Models\Rekap;
use App\Models\CatatanOperasional;
use App\Models\Kloter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class RekapController extends Controller
{
    public function pilihTanggal($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $pegawai = \App\Models\Pegawai::find(Session::get('user')->id);
        if (!$pegawai || $pegawai->outlet_id != $outlet_id) {
            abort(403, 'Akses ditolak.');
        }

        $outlet = Outlet::findOrFail($outlet_id);

        // Ambil operasional yang:
        // 1. Belum memiliki rekap
        // 2. Memiliki transaksi (artinya ada aktivitas operasional)
        // 3. Status 'aktif' atau 'selesai' (bukan yang dibatalkan)
        // 4. Maksimal 30 hari terakhir
        $operasionalsWithoutRekap = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', '>=', Carbon::now()->subDays(30)->toDateString())
            ->whereDoesntHave('rekap')
            ->whereHas('transaksis') // Hanya operasional yang memiliki transaksi
            ->whereIn('status', ['aktif', 'selesai'])
            ->orderBy('tanggal', 'desc')
            ->get();

        return view('pegawai.pilih-tanggal-rekap', compact('outlet', 'operasionalsWithoutRekap'));
    }

    public function rekap($outlet_id, Request $request)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $pegawai = \App\Models\Pegawai::find(Session::get('user')->id);
        if (!$pegawai || $pegawai->outlet_id != $outlet_id) {
            abort(403, 'Akses ditolak.');
        }
        $outlet = Outlet::findOrFail($outlet_id);

        // Ambil tanggal dari parameter atau gunakan hari ini
        $tanggal = $request->input('tanggal', Carbon::today()->toDateString());

        // Validasi format tanggal
        try {
            $tanggal = Carbon::parse($tanggal)->toDateString();
        } catch (\Exception $e) {
            $tanggal = Carbon::today()->toDateString();
        }

        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', $tanggal)
            ->first();

        if (!$operasional) {
            // Jika operasional tidak ditemukan
            Session::flash('error', 'Operasional untuk tanggal ' . Carbon::parse($tanggal)->format('d-m-Y') . ' tidak ditemukan.');
            return redirect()->route('pegawai.dashboard');
        }

        // Cek apakah sudah ada rekap untuk operasional ini
        $existingRekap = Rekap::where('operasional_id', $operasional->id)->first();
        if ($existingRekap) {
            Session::flash('info', 'Rekap untuk tanggal ' . Carbon::parse($tanggal)->format('d-m-Y') . ' sudah dibuat.');
            return redirect()->route('pegawai.rekap.detail', [$outlet_id, $existingRekap->id]);
        }

        if ($operasional->status !== 'aktif') {
            // Jika operasional tidak aktif, tampilkan halaman rekap dengan data kosong
            $transaksis = collect();
            $totalDonatTerjual = 0;
            $totalUangPenjualan = 0;
            $kloters = collect();
            $totalDonat = 0;
            $usedMika = 0;
            $usedDus1 = 0;
            $usedDus2 = 0;
            $usedDus3 = 0;
            $usedBox = 0;
            $usedBox12 = 0;
            $totalMika = 0;
            $totalDus1 = 0;
            $totalDus2 = 0;
            $totalDus3 = 0;
            $totalBox = 0;
            $totalBox12 = 0;
            $totalPendapatan = 0;
            $tempCatatan = [];

            Session::flash('info', 'Operasional belum dimulai atau sudah selesai. Anda dapat melihat histori rekap di menu Histori Rekap.');

            return view('pegawai.rekap', compact(
                'outlet',
                'operasional',
                'kloters',
                'totalDonat',
                'totalDonatTerjual',
                'totalMika',
                'totalDus1',
                'totalDus2',
                'totalDus3',
                'totalBox',
                'totalBox12',
                'usedMika',
                'usedDus1',
                'usedDus2',
                'usedDus3',
                'usedBox',
                'usedBox12',
                'totalUangPenjualan',
                'totalPendapatan',
                'tempCatatan',
                'tanggal'
            ));
        }

        $transaksis = Transaksi::where('operasional_id', $operasional->id)->with('items')->get();
        $totalDonatTerjual = $transaksis->sum('total_donat');
        $totalUangPenjualan = $transaksis->sum('total_harga');

        // Ambil data kloter untuk operasional hari ini
        $kloters = Kloter::where('operasional_id', $operasional->id)->get();
        $totalDonat = $kloters->sum('jumlah_donat');

        // Hitung penggunaan stok kemasan
        $usedMika = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'mika')->sum('jumlah');
        });
        $usedDus1 = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'dus1')->sum('jumlah');
        });
        $usedDus2 = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'dus2')->sum('jumlah');
        });
        $usedDus3 = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'dus3')->sum('jumlah');
        });
        $usedBox = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'box')->sum('jumlah');
        });
        $usedBox12 = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'box12')->sum('jumlah');
        });
        $usedLilin = $transaksis->sum(function ($t) {
            return $t->items->where('kemasan', 'lilin')->sum('jumlah');
        });

        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0,
            'stok_dus1' => 0,
            'stok_dus2' => 0,
            'stok_dus3' => 0,
            'stok_box' => 0,
            'stok_box12' => 0,
            'stok_lilin' => 0,
        ]);
        $totalMika = $stokOutlet->stok_mika;
        $totalDus1 = $stokOutlet->stok_dus1;
        $totalDus2 = $stokOutlet->stok_dus2;
        $totalDus3 = $stokOutlet->stok_dus3;
        $totalBox = $stokOutlet->stok_box;
        $totalBox12 = $stokOutlet->stok_box12;
        $totalLilin = $stokOutlet->stok_lilin;

        $tempCatatan = Session::get('catatan_operasionals', []);
        $totalPemasukan = array_sum(array_column(array_filter($tempCatatan, fn($c) => $c['jenis'] === 'pemasukan'), 'jumlah'));
        $totalPengeluaran = array_sum(array_column(array_filter($tempCatatan, fn($c) => $c['jenis'] === 'pengeluaran'), 'jumlah'));
        $totalPendapatan = $totalUangPenjualan + $totalPemasukan - $totalPengeluaran;

        return view('pegawai.rekap', compact(
            'outlet',
            'operasional',
            'kloters',
            'totalDonat',
            'totalDonatTerjual',
            'totalMika',
            'totalDus1',
            'totalDus2',
            'totalDus3',
            'totalBox',
            'totalBox12',
            'totalLilin',
            'usedMika',
            'usedDus1',
            'usedDus2',
            'usedDus3',
            'usedBox',
            'usedBox12',
            'usedLilin',
            'totalUangPenjualan',
            'totalPendapatan',
            'tempCatatan',
            'tanggal'
        ));
    }

    public function tambahCatatan(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'jenis' => 'required|in:pemasukan,pengeluaran',
                'jumlah' => 'required|integer|min:0',
                'catatan' => 'nullable|string|max:255',
            ]);

            $catatan = [
                'jenis' => $request->jenis,
                'jumlah' => $request->jumlah,
                'catatan' => $request->catatan,
            ];

            $tempCatatan = Session::get('catatan_operasionals', []);
            $tempCatatan[] = $catatan;
            Session::put('catatan_operasionals', $tempCatatan);

            return response()->json(['success' => 'Catatan berhasil ditambahkan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan catatan'], 500);
        }
    }

    public function hapusCatatan(Request $request)
    {
        try {
            $request->validate(['index' => 'required|integer']);
            $tempCatatan = Session::get('catatan_operasionals', []);
            if (isset($tempCatatan[$request->index])) {
                unset($tempCatatan[$request->index]);
                $tempCatatan = array_values($tempCatatan); // Reindex array
                Session::put('catatan_operasionals', $tempCatatan);
                return response()->json(['success' => 'Catatan berhasil dihapus']);
            }
            return response()->json(['error' => 'Catatan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus catatan'], 500);
        }
    }

    public function simpanRekap(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'operasional_id' => 'required|exists:operasionals,id',
                'sisa_mika' => 'required|integer|min:0',
                'sisa_dus1' => 'required|integer|min:0',
                'sisa_dus2' => 'required|integer|min:0',
                'sisa_dus3' => 'required|integer|min:0',
                'sisa_box' => 'required|integer|min:0',
                'sisa_box12' => 'required|integer|min:0',
                'sisa_lilin' => 'required|integer|min:0',
                'total_uang' => 'required|integer|min:0',
            ]);

            $operasional = Operasional::findOrFail($request->operasional_id);
            if ($operasional->status !== 'aktif') {
                Session::flash('error', 'Operasional sudah selesai atau belum dimulai.');
                return redirect()->back();
            }

            $transaksis = Transaksi::where('operasional_id', $operasional->id)->with('items')->get();
            $totalDonatTerjual = $transaksis->sum('total_donat');
            $totalUangPenjualan = $transaksis->sum('total_harga');

            // Hitung penggunaan stok kemasan
            $usedMika = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'mika')->sum('jumlah');
            });
            $usedDus1 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'dus1')->sum('jumlah');
            });
            $usedDus2 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'dus2')->sum('jumlah');
            });
            $usedDus3 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'dus3')->sum('jumlah');
            });
            $usedBox = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'box')->sum('jumlah');
            });
            $usedBox12 = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'box12')->sum('jumlah');
            });
            $usedLilin = $transaksis->sum(function ($t) {
                return $t->items->where('kemasan', 'lilin')->sum('jumlah');
            });

            $stokOutlet = StokOutlet::where('outlet_id', $request->outlet_id)->firstOrFail();
            $stokOutlet->update([
                'stok_mika' => $request->sisa_mika,
                'stok_dus1' => $request->sisa_dus1,
                'stok_dus2' => $request->sisa_dus2,
                'stok_dus3' => $request->sisa_dus3,
                'stok_box' => $request->sisa_box,
                'stok_box12' => $request->sisa_box12,
                'stok_lilin' => $request->sisa_lilin,
            ]);

            // Hitung total uang per metode pembayaran
            $totalTunai = $transaksis->where('metode_pembayaran', 'tunai')->sum('total_harga');
            $totalQris = $transaksis->where('metode_pembayaran', 'qris')->sum('total_harga');
            $totalTransfer = $transaksis->where('metode_pembayaran', 'transfer')->sum('total_harga');
            $totalGrabfood = $transaksis->where('metode_pembayaran', 'grabfood')->sum('total_harga');
            $totalGofood = $transaksis->where('metode_pembayaran', 'gofood')->sum('total_harga');
            $totalMaxim = 0; // Tidak digunakan untuk sementara

            // Hitung cash di pegawai = total tunai + (pemasukan - pengeluaran) dari catatan
            $tempCatatan = Session::get('catatan_operasionals', []);
            $netCatatan = 0;
            foreach ($tempCatatan as $catatan) {
                if (($catatan['jenis'] ?? '') === 'pemasukan') {
                    $netCatatan += (int) ($catatan['jumlah'] ?? 0);
                } elseif (($catatan['jenis'] ?? '') === 'pengeluaran') {
                    $netCatatan -= (int) ($catatan['jumlah'] ?? 0);
                }
            }
            $cashDiPegawai = (int) $totalTunai + (int) $netCatatan;

            $rekap = Rekap::create([
                'outlet_id' => $request->outlet_id,
                'operasional_id' => $request->operasional_id,
                'pegawai_id' => Session::get('user')->id,
                'total_donat_terjual' => $totalDonatTerjual,
                'sisa_mika' => $request->sisa_mika,
                'sisa_dus1' => $request->sisa_dus1,
                'sisa_dus2' => $request->sisa_dus2,
                'sisa_dus3' => $request->sisa_dus3,
                'sisa_box' => $request->sisa_box,
                'sisa_box12' => $request->sisa_box12,
                'sisa_lilin' => $request->sisa_lilin,
                'used_mika' => $usedMika,
                'used_dus1' => $usedDus1,
                'used_dus2' => $usedDus2,
                'used_dus3' => $usedDus3,
                'used_box' => $usedBox,
                'used_box12' => $usedBox12,
                'used_lilin' => $usedLilin,
                'total_uang_penjualan' => $totalUangPenjualan,
                'total_uang' => $request->total_uang,
                'total_tunai' => $totalTunai,
                'total_qris' => $totalQris,
                'total_transfer' => $totalTransfer,
                'total_maxim' => $totalMaxim,
                'total_grabfood' => $totalGrabfood,
                'total_gofood' => $totalGofood,
                'cash_di_pegawai' => $cashDiPegawai,
                'tanggal' => $operasional->tanggal,
                'status' => 'pending',
            ]);

            foreach ($tempCatatan as $catatan) {
                CatatanOperasional::create([
                    'rekap_id' => $rekap->id,
                    'operasional_id' => $request->operasional_id,
                    'outlet_id' => $request->outlet_id,
                    'pegawai_id' => Session::get('user')->id,
                    'jenis' => $catatan['jenis'],
                    'jumlah' => $catatan['jumlah'],
                    'catatan' => $catatan['catatan'] ?? '',
                ]);
            }

            Session::forget('catatan_operasionals');
            Session::flash('success', 'Rekap harian berhasil disimpan.');
            return redirect()->route('pegawai.rekap.detail', [$request->outlet_id, $rekap->id]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $allErrors = [];
            foreach ($e->errors() as $fieldErrors) {
                $allErrors = array_merge($allErrors, (array) $fieldErrors);
            }
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $allErrors));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menyimpan rekap: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function rekapDetail($outlet_id, $rekap_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $pegawai = \App\Models\Pegawai::find(Session::get('user')->id);
        if (!$pegawai || $pegawai->outlet_id != $outlet_id) {
            abort(403, 'Akses ditolak.');
        }
        $outlet = Outlet::findOrFail($outlet_id);
        $rekap = Rekap::where('id', $rekap_id)
            ->where('outlet_id', $outlet_id)
            ->with([
                'catatanOperasionals',
                'operasional.transaksis.items'
            ])
            ->firstOrFail();

        // Ambil data dari rekap yang sudah tersimpan
        $totalDonatTerjual = $rekap->total_donat_terjual;
        $totalMika = $rekap->sisa_mika;
        $totalDus1 = $rekap->sisa_dus1;
        $totalDus2 = $rekap->sisa_dus2;
        $totalDus3 = $rekap->sisa_dus3;
        $totalBox = $rekap->sisa_box;
        $totalBox12 = $rekap->sisa_box12;
        $usedMika = $rekap->used_mika;
        $usedDus1 = $rekap->used_dus1;
        $usedDus2 = $rekap->used_dus2;
        $usedDus3 = $rekap->used_dus3;
        $usedBox = $rekap->used_box;
        $usedBox12 = $rekap->used_box12;
        $totalUang = $rekap->total_uang;
        $catatanOperasionals = $rekap->catatanOperasionals;

        return view('pegawai.rekap-detail', compact(
            'outlet',
            'rekap',
            'totalDonatTerjual',
            'totalMika',
            'totalDus1',
            'totalDus2',
            'totalDus3',
            'totalBox',
            'totalBox12',
            'usedMika',
            'usedDus1',
            'usedDus2',
            'usedDus3',
            'usedBox',
            'usedBox12',
            'totalUang',
            'catatanOperasionals'
        ));
    }

    public function destroyRekap($outlet_id, $rekap_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $pegawai = \App\Models\Pegawai::find(Session::get('user')->id);
        if (!$pegawai || $pegawai->outlet_id != $outlet_id) {
            abort(403, 'Akses ditolak.');
        }

        $rekap = Rekap::where('id', $rekap_id)
            ->where('outlet_id', $outlet_id)
            ->firstOrFail();

        if ($rekap->pegawai_id !== $pegawai->id) {
            abort(403, 'Anda tidak berhak menghapus rekap ini.');
        }

        if ($rekap->status !== 'pending') {
            Session::flash('error', 'Rekap sudah divalidasi/tidak dapat dihapus.');
            return redirect()->route('pegawai.rekap.detail', [$outlet_id, $rekap_id]);
        }

        // Hapus catatan operasional terkait
        CatatanOperasional::where('rekap_id', $rekap->id)->delete();

        // Tidak mengubah transaksi. Kembalikan stok kemasan ke nilai sebelumnya berdasarkan sisa_* di rekap
        // Asumsi: saat simpanRekap, sisa_* menjadi stok saat itu; menghapus rekap tidak mengubah transaksi,
        // dan stok outlet tidak perlu di-rollback karena sisa_* sudah menjadi stok terkini.

        $rekap->delete();

        Session::flash('success', 'Rekap berhasil dihapus. Silakan buat ulang jika diperlukan.');
        return redirect()->route('pegawai.dashboard');
    }
}
