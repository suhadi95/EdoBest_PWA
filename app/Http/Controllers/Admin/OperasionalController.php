<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Rekap;
use App\Models\Kloter;
use App\Models\Outlet;
use App\Models\StokOutlet;
use App\Models\HistoriStok;
use App\Models\Operasional;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class OperasionalController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlets = Outlet::with([
            'operasionals' => function ($query) {
                $query->where('tanggal', Carbon::today()->toDateString());
            },
            'operasionals.rekap'
        ])->get();
        return view('admin.operasional-harian', compact('outlets'));
    }

    public function mulaiOperasional(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
            ]);

            $existingOperasional = Operasional::where('outlet_id', $request->outlet_id)
                ->where('tanggal', Carbon::today()->toDateString())
                ->first();

            if ($existingOperasional) {
                Session::flash('error', 'Operasional untuk outlet ini sudah dimulai hari ini.');
                return redirect()->back();
            }

            $outlet = Outlet::findOrFail($request->outlet_id);

            Operasional::create([
                'outlet_id' => $request->outlet_id,
                'tanggal' => Carbon::today()->toDateString(),
                'status' => 'aktif',
                'total_donat_harian' => 0,
                'biaya_listrik' => (int) ($outlet->biaya_listrik_harian ?? 0),
            ]);

            Session::flash('success', 'Operasional berhasil dimulai.');
            return redirect()->route('operasional.detail', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memulai operasional: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function detail($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::findOrFail($outlet_id);
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0,
            'stok_dus1' => 0,
            'stok_dus2' => 0,
            'stok_dus3' => 0,
            'stok_box' => 0,
            'stok_box12' => 0,
            'stok_lilin' => 0,
        ]);

        // Gunakan stok langsung dari stok_outlets
        $totalMika = $stokOutlet->stok_mika;
        $totalDus1 = $stokOutlet->stok_dus1;
        $totalDus2 = $stokOutlet->stok_dus2;
        $totalDus3 = $stokOutlet->stok_dus3;
        $totalBox = $stokOutlet->stok_box;
        $totalBox12 = $stokOutlet->stok_box12;

        $kloters = $operasional ? $operasional->kloters : collect([]);
        $rekap = $operasional ? Rekap::where('operasional_id', $operasional->id)->with('catatanOperasionals', 'operasional.transaksis')->first() : null;

        // Calculate additional variables for the new modal design
        $totalDonat = $kloters->sum('jumlah_donat');
        $transaksis = $operasional ? Transaksi::where('operasional_id', $operasional->id)->with('items')->get() : collect([]);
        $totalDonatTerjual = $transaksis->sum('total_donat');
        $totalUangPenjualan = $transaksis->sum('total_harga');

        // Calculate used packaging
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

        // Calculate total pendapatan
        $totalPendapatan = $totalUangPenjualan;
        if ($rekap && $rekap->catatanOperasionals) {
            foreach ($rekap->catatanOperasionals as $catatan) {
                if ($catatan->jenis === 'pemasukan' && !$catatan->kategori_kemasan) {
                    $totalPendapatan += $catatan->jumlah;
                } elseif ($catatan->jenis === 'pengeluaran' && !$catatan->kategori_kemasan) {
                    $totalPendapatan -= $catatan->jumlah;
                }
            }
        }

        // Calculate total tunai
        $totalTunai = 0;
        if ($operasional) {
            $totalTunai = Transaksi::where('operasional_id', $operasional->id)
                ->where('metode_pembayaran', 'tunai')
                ->sum('total_harga');
        }

        // Calculate total catatan operasional (pemasukan + pengeluaran)
        $totalCatatanOperasional = 0;
        if ($rekap && $rekap->catatanOperasionals) {
            foreach ($rekap->catatanOperasionals as $catatan) {
                if (!$catatan->kategori_kemasan) {
                    if ($catatan->jenis === 'pemasukan') {
                        $totalCatatanOperasional += $catatan->jumlah;
                    } elseif ($catatan->jenis === 'pengeluaran') {
                        $totalCatatanOperasional -= $catatan->jumlah;
                    }
                }
            }
        }

        // Calculate cash di pegawai
        $cashPegawai = $totalTunai + $totalCatatanOperasional;

        return view('admin.detail-operasional', compact(
            'outlet',
            'operasional',
            'kloters',
            'rekap',
            'stokOutlet',
            'totalMika',
            'totalDus1',
            'totalDus2',
            'totalDus3',
            'totalBox',
            'totalDonat',
            'totalDonatTerjual',
            'totalUangPenjualan',
            'totalPendapatan',
            'cashPegawai',
            'outlet',
            'operasional',
            'kloters',
            'rekap',
            'stokOutlet',
            'totalMika',
            'totalDus1',
            'totalDus2',
            'totalDus3',
            'totalBox',
            'totalBox12',
            'totalDonat',
            'totalDonatTerjual',
            'totalUangPenjualan',
            'totalPendapatan',
            'cashPegawai',
            'usedMika',
            'usedDus1',
            'usedDus2',
            'usedDus3',
            'usedBox',
            'usedBox12'
        ));
    }

    public function tambahKloter(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'operasional_id' => 'required|exists:operasionals,id',
                'jumlah_donat' => 'required|integer|min:1',
                'jumlah_mika' => 'nullable|integer|min:0',
                'jumlah_dus1' => 'nullable|integer|min:0',
                'jumlah_dus2' => 'nullable|integer|min:0',
                'jumlah_dus3' => 'nullable|integer|min:0',
                'jumlah_box' => 'nullable|integer|min:0',
                'jumlah_box12' => 'nullable|integer|min:0',
                'jumlah_lilin' => 'nullable|integer|min:0',
            ]);

            $operasional = Operasional::findOrFail($request->operasional_id);
            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $operasional->outlet_id], [
                'stok_mika' => 0,
                'stok_dus1' => 0,
                'stok_dus2' => 0,
                'stok_dus3' => 0,
                'stok_box' => 0,
                'stok_box12' => 0,
                'stok_lilin' => 0,
            ]);

            $kloter = Kloter::create([
                'operasional_id' => $request->operasional_id,
                'jumlah_donat' => $request->jumlah_donat,
                'jumlah_mika' => $request->jumlah_mika ?? 0,
                'jumlah_dus1' => $request->jumlah_dus1 ?? 0,
                'jumlah_dus2' => $request->jumlah_dus2 ?? 0,
                'jumlah_dus3' => $request->jumlah_dus3 ?? 0,
                'jumlah_box' => $request->jumlah_box ?? 0,
                'jumlah_box12' => $request->jumlah_box12 ?? 0,
                'jumlah_lilin' => $request->jumlah_lilin ?? 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update stok_outlets
            $stokOutlet->stok_mika += $request->jumlah_mika;
            $stokOutlet->stok_dus1 += $request->jumlah_dus1;
            $stokOutlet->stok_dus2 += $request->jumlah_dus2;
            $stokOutlet->stok_dus3 += $request->jumlah_dus3;
            $stokOutlet->stok_box += $request->jumlah_box;
            $stokOutlet->stok_box12 += $request->jumlah_box12;
            $stokOutlet->stok_lilin += $request->jumlah_lilin;
            $stokOutlet->save();

            // Catat histori stok
            $today = Carbon::today()->toDateString();
            $kloterCount = Kloter::where('operasional_id', $request->operasional_id)
                ->whereDate('created_at', $today)
                ->count();
            $keterangan = "Kloter {$kloterCount} ({$today})";

            if ($request->jumlah_mika > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'mika',
                    'jumlah_perubahan' => $request->jumlah_mika,
                    'keterangan' => $keterangan,
                ]);
            }
            if ($request->jumlah_dus1 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'dus1',
                    'jumlah_perubahan' => $request->jumlah_dus1,
                    'keterangan' => $keterangan,
                ]);
            }
            if ($request->jumlah_dus2 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'dus2',
                    'jumlah_perubahan' => $request->jumlah_dus2,
                    'keterangan' => $keterangan,
                ]);
            }
            if ($request->jumlah_dus3 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'dus3',
                    'jumlah_perubahan' => $request->jumlah_dus3,
                    'keterangan' => $keterangan,
                ]);
            }
            if ($request->jumlah_box > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'box',
                    'jumlah_perubahan' => $request->jumlah_box,
                    'keterangan' => $keterangan,
                ]);
            }
            if ($request->jumlah_box12 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'box12',
                    'jumlah_perubahan' => $request->jumlah_box12,
                    'keterangan' => $keterangan,
                ]);
            }
            if ($request->jumlah_lilin > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'lilin',
                    'jumlah_perubahan' => $request->jumlah_lilin,
                    'keterangan' => $keterangan,
                ]);
            }

            // Update total_donat_harian
            $operasional->total_donat_harian += $request->jumlah_donat;
            $operasional->save();

            Session::flash('success', 'Kloter berhasil ditambahkan.');
            return redirect()->back();
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menambah kloter: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function hapusKloter($kloter_id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $kloter = Kloter::findOrFail($kloter_id);
            $operasional = $kloter->operasional;
            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $operasional->outlet_id], [
                'stok_mika' => 0,
                'stok_dus1' => 0,
                'stok_dus2' => 0,
                'stok_dus3' => 0,
                'stok_box' => 0,
                'stok_box12' => 0,
                'stok_lilin' => 0,
            ]);

            // Reverse stok_outlets
            $stokOutlet->stok_mika -= $kloter->jumlah_mika;
            $stokOutlet->stok_dus1 -= $kloter->jumlah_dus1;
            $stokOutlet->stok_dus2 -= $kloter->jumlah_dus2;
            $stokOutlet->stok_dus3 -= $kloter->jumlah_dus3;
            $stokOutlet->stok_box -= $kloter->jumlah_box;
            $stokOutlet->stok_box12 -= $kloter->jumlah_box12;
            $stokOutlet->stok_lilin -= $kloter->jumlah_lilin;
            $stokOutlet->save();

            // Delete histori stok for this kloter
            $today = Carbon::today()->toDateString();
            $kloterCount = Kloter::where('operasional_id', $operasional->id)
                ->whereDate('created_at', $today)
                ->count();
            $keterangan = "Kloter {$kloterCount} ({$today})";
            HistoriStok::where('outlet_id', $operasional->outlet_id)
                ->where('keterangan', $keterangan)
                ->delete();

            // Update total_donat_harian
            $operasional->total_donat_harian -= $kloter->jumlah_donat;
            $operasional->save();

            // Delete kloter
            $kloter->delete();

            Session::flash('success', 'Kloter berhasil dihapus.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menghapus kloter: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function updateStok(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'jenis_stok' => 'required|in:mika,dus1,dus2,dus3,box,box12,lilin',
                'jumlah_perubahan' => 'required|integer',
                'keterangan' => 'required|string|max:255',
            ]);

            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $request->outlet_id]);

            // Hitung nomor urut update manual untuk hari ini
            $today = Carbon::today()->toDateString();
            $manualUpdateCount = HistoriStok::where('outlet_id', $request->outlet_id)
                ->where('keterangan', 'like', 'Update Manual%')
                ->whereDate('created_at', $today)
                ->count() + 1;

            switch ($request->jenis_stok) {
                case 'mika':
                    $stokOutlet->stok_mika += $request->jumlah_perubahan;
                    break;
                case 'dus1':
                    $stokOutlet->stok_dus1 += $request->jumlah_perubahan;
                    break;
                case 'dus2':
                    $stokOutlet->stok_dus2 += $request->jumlah_perubahan;
                    break;
                case 'dus3':
                    $stokOutlet->stok_dus3 += $request->jumlah_perubahan;
                    break;
                case 'box':
                    $stokOutlet->stok_box += $request->jumlah_perubahan;
                    break;
                case 'box12':
                    $stokOutlet->stok_box12 += $request->jumlah_perubahan;
                    break;
                case 'lilin':
                    $stokOutlet->stok_lilin += $request->jumlah_perubahan;
                    break;
            }
            $stokOutlet->save();

            // Catat histori
            HistoriStok::create([
                'outlet_id' => $request->outlet_id,
                'jenis_stok' => $request->jenis_stok,
                'jumlah_perubahan' => $request->jumlah_perubahan,
                'keterangan' => "Update Manual {$manualUpdateCount} ({$today}): {$request->keterangan}",
            ]);

            Session::flash('success', 'Stok berhasil diupdate.');
            return redirect()->route('stok.detail', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal mengupdate stok: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}