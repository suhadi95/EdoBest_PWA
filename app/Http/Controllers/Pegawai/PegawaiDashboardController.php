<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Operasional;
use App\Models\Kloter;
use App\Models\StokOutlet;
use App\Models\Transaksi;
use App\Models\TransaksiItem;
use App\Models\HargaItem;
use App\Models\HistoriStok;
use App\Models\Rekap;
use App\Models\CatatanOperasional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class PegawaiDashboardController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $pegawai_id = Session::get('user')->id;
        $outlets = Outlet::where('pegawai_id', $pegawai_id)
            ->with(['operasionals' => function ($query) {
                $query->where('tanggal', Carbon::today()->toDateString());
            }])
            ->get();

        return view('pegawai.dashboard', compact('outlets'));
    }

    public function penjualan($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::where('id', $outlet_id)->where('pegawai_id', Session::get('user')->id)->firstOrFail();
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$operasional) {
            Session::flash('error', 'Operasional belum dimulai hari ini.');
            return redirect()->route('pegawai.dashboard');
        }

        $kloters = $operasional->kloters;
        $totalKloter = $kloters->count();
        $totalDonat = $kloters->sum('jumlah_donat');
        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0, 'stok_dus1' => 0, 'stok_dus2' => 0, 'stok_dus3' => 0, 'stok_box' => 0
        ]);
        $totalMika = $stokOutlet->stok_mika + $kloters->sum('jumlah_mika');
        $totalDus1 = $stokOutlet->stok_dus1 + $kloters->sum('jumlah_dus1');
        $totalDus2 = $stokOutlet->stok_dus2 + $kloters->sum('jumlah_dus2');
        $totalDus3 = $stokOutlet->stok_dus3 + $kloters->sum('jumlah_dus3');
        $totalBox = $stokOutlet->stok_box + $kloters->sum('jumlah_box');

        $transaksis = Transaksi::where('operasional_id', $operasional->id)
            ->where('outlet_id', $outlet_id)
            ->latest()
            ->get();

        return view('pegawai.penjualan', compact('outlet', 'operasional', 'totalKloter', 'totalDonat', 'totalMika', 'totalDus1', 'totalDus2', 'totalDus3', 'totalBox', 'transaksis'));
    }

    public function transaksi($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::where('id', $outlet_id)->where('pegawai_id', Session::get('user')->id)->firstOrFail();
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$operasional) {
            Session::flash('error', 'Operasional belum dimulai hari ini.');
            return redirect()->route('pegawai.dashboard');
        }

        $transaksiCount = Transaksi::where('operasional_id', $operasional->id)->count() + 1;
        $tempItems = Session::get('transaksi_items', []);

        return view('pegawai.transaksi', compact('outlet', 'operasional', 'transaksiCount', 'tempItems'));
    }

    public function tambahItem(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'kemasan' => 'required|in:mika,dus1,dus2,dus3,box',
                'jumlah' => 'required|integer|min:1',
                'tipe' => 'required|in:original,klasik',
            ]);

            $donatPerItem = [
                'mika' => 1,
                'dus1' => 1,
                'dus2' => 2,
                'dus3' => 3,
                'box' => 6,
            ];

            $hargaItem = HargaItem::where('nama_item', $request->kemasan)->firstOrFail();
            $harga = $request->tipe === 'original' ? $hargaItem->harga_original : $hargaItem->harga_klasik;
            $totalHarga = $request->jumlah * $harga;
            $donat = $request->jumlah * $donatPerItem[$request->kemasan];

            $item = [
                'kemasan' => $request->kemasan,
                'jumlah' => $request->jumlah,
                'tipe' => $request->tipe,
                'donat_per_item' => $donatPerItem[$request->kemasan],
                'total_harga' => $totalHarga,
            ];

            $tempItems = Session::get('transaksi_items', []);
            $tempItems[] = $item;
            Session::put('transaksi_items', $tempItems);

            return response()->json(['success' => 'Item berhasil ditambahkan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan item'], 500);
        }
    }

    public function hapusItem(Request $request)
    {
        try {
            $request->validate(['index' => 'required|integer']);
            $tempItems = Session::get('transaksi_items', []);
            if (isset($tempItems[$request->index])) {
                unset($tempItems[$request->index]);
                $tempItems = array_values($tempItems); // Reindex array
                Session::put('transaksi_items', $tempItems);
                return response()->json(['success' => 'Item berhasil dihapus']);
            }
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus item'], 500);
        }
    }

    public function simpanTransaksi(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'operasional_id' => 'required|exists:operasionals,id',
                'metode_pembayaran' => 'required|in:tunai,qris,transfer',
            ]);

            $tempItems = Session::get('transaksi_items', []);
            if (empty($tempItems)) {
                Session::flash('error', 'Tidak ada item transaksi.');
                return redirect()->back();
            }

            $operasional = Operasional::findOrFail($request->operasional_id);
            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $request->outlet_id], [
                'stok_mika' => 0, 'stok_dus1' => 0, 'stok_dus2' => 0, 'stok_dus3' => 0, 'stok_box' => 0
            ]);

            $kloters = $operasional->kloters;
            $totalDonatTersedia = $operasional->total_donat_harian;
            $totalDonatTransaksi = array_sum(array_map(function ($item) {
                return $item['jumlah'] * $item['donat_per_item'];
            }, $tempItems));

            // Validasi stok donat
            if ($totalDonatTransaksi > $totalDonatTersedia) {
                Session::flash('error', 'Stok donat tidak cukup. Tersedia: ' . $totalDonatTersedia . ' donat.');
                return redirect()->back();
            }

            // Validasi stok kemasan
            $stokTersedia = [
                'mika' => $stokOutlet->stok_mika + $kloters->sum('jumlah_mika'),
                'dus1' => $stokOutlet->stok_dus1 + $kloters->sum('jumlah_dus1'),
                'dus2' => $stokOutlet->stok_dus2 + $kloters->sum('jumlah_dus2'),
                'dus3' => $stokOutlet->stok_dus3 + $kloters->sum('jumlah_dus3'),
                'box' => $stokOutlet->stok_box + $kloters->sum('jumlah_box'),
            ];

            foreach ($tempItems as $item) {
                if ($item['jumlah'] > $stokTersedia[$item['kemasan']]) {
                    Session::flash('error', 'Stok ' . $item['kemasan'] . ' tidak cukup. Tersedia: ' . $stokTersedia[$item['kemasan']] . '.');
                    return redirect()->back();
                }
            }

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'outlet_id' => $request->outlet_id,
                'operasional_id' => $request->operasional_id,
                'pegawai_id' => Session::get('user')->id,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_donat' => $totalDonatTransaksi,
                'total_harga' => array_sum(array_column($tempItems, 'total_harga')),
            ]);

            // Simpan item transaksi dan kurangi stok
            foreach ($tempItems as $item) {
                TransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'kemasan' => $item['kemasan'],
                    'jumlah' => $item['jumlah'],
                    'tipe' => $item['tipe'],
                    'donat_per_item' => $item['donat_per_item'],
                    'total_harga' => $item['total_harga'],
                ]);

                // Kurangi stok kemasan
                switch ($item['kemasan']) {
                    case 'mika':
                        $stokOutlet->stok_mika -= $item['jumlah'];
                        break;
                    case 'dus1':
                        $stokOutlet->stok_dus1 -= $item['jumlah'];
                        break;
                    case 'dus2':
                        $stokOutlet->stok_dus2 -= $item['jumlah'];
                        break;
                    case 'dus3':
                        $stokOutlet->stok_dus3 -= $item['jumlah'];
                        break;
                    case 'box':
                        $stokOutlet->stok_box -= $item['jumlah'];
                        break;
                }

                // Catat histori stok kemasan
                HistoriStok::create([
                    'outlet_id' => $request->outlet_id,
                    'jenis_stok' => $item['kemasan'],
                    'jumlah_perubahan' => -$item['jumlah'],
                    'keterangan' => 'Transaksi ke-' . $transaksi->id,
                ]);
            }

            // Kurangi stok donat
            $operasional->total_donat_harian -= $totalDonatTransaksi;
            $operasional->save();

            // Catat histori stok donat
            HistoriStok::create([
                'outlet_id' => $request->outlet_id,
                'jenis_stok' => 'donat',
                'jumlah_perubahan' => -$totalDonatTransaksi,
                'keterangan' => 'Transaksi ke-' . $transaksi->id,
            ]);

            $stokOutlet->save();

            Session::forget('transaksi_items');
            Session::flash('success', 'Transaksi berhasil disimpan.');
            return redirect()->route('pegawai.penjualan', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menyimpan transaksi: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function detailTransaksi($outlet_id, $transaksi_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::where('id', $outlet_id)->where('pegawai_id', Session::get('user')->id)->firstOrFail();
        $transaksi = Transaksi::where('id', $transaksi_id)
            ->where('outlet_id', $outlet_id)
            ->with('items')
            ->firstOrFail();
        $operasional = Operasional::findOrFail($transaksi->operasional_id);

        return view('pegawai.transaksi', compact('outlet', 'transaksi', 'operasional'));
    }

    public function rekap($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::where('id', $outlet_id)->where('pegawai_id', Session::get('user')->id)->firstOrFail();
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$operasional || $operasional->status !== 'aktif') {
            Session::flash('error', 'Operasional belum dimulai atau sudah selesai.');
            return redirect()->route('pegawai.dashboard');
        }

        $transaksis = Transaksi::where('operasional_id', $operasional->id)->get();
        $totalDonatTerjual = $transaksis->sum('total_donat');
        $totalUangPenjualan = $transaksis->sum('total_harga');

        $kloters = $operasional->kloters;
        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0, 'stok_dus1' => 0, 'stok_dus2' => 0, 'stok_dus3' => 0, 'stok_box' => 0
        ]);
        $totalMika = $stokOutlet->stok_mika + $kloters->sum('jumlah_mika');
        $totalDus1 = $stokOutlet->stok_dus1 + $kloters->sum('jumlah_dus1');
        $totalDus2 = $stokOutlet->stok_dus2 + $kloters->sum('jumlah_dus2');
        $totalDus3 = $stokOutlet->stok_dus3 + $kloters->sum('jumlah_dus3');
        $totalBox = $stokOutlet->stok_box + $kloters->sum('jumlah_box');

        $tempCatatan = Session::get('catatan_operasionals', []);
        $totalPemasukkan = array_sum(array_column(array_filter($tempCatatan, fn($c) => $c['jenis'] === 'pemasukkan'), 'jumlah'));
        $totalPengeluaran = array_sum(array_column(array_filter($tempCatatan, fn($c) => $c['jenis'] === 'pengeluaran'), 'jumlah'));
        $totalUang = $totalUangPenjualan + $totalPemasukkan - $totalPengeluaran;

        return view('pegawai.rekap', compact(
            'outlet',
            'operasional',
            'totalDonatTerjual',
            'totalMika',
            'totalDus1',
            'totalDus2',
            'totalDus3',
            'totalBox',
            'totalUang',
            'tempCatatan'
        ));
    }

    public function tambahCatatan(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'jenis' => 'required|in:pemasukkan,pengeluaran',
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
            ]);

            $operasional = Operasional::findOrFail($request->operasional_id);
            if ($operasional->status !== 'aktif') {
                Session::flash('error', 'Operasional sudah selesai atau belum dimulai.');
                return redirect()->back();
            }

            $transaksis = Transaksi::where('operasional_id', $operasional->id)->get();
            $totalDonatTerjual = $transaksis->sum('total_donat');
            $totalUangPenjualan = $transaksis->sum('total_harga');

            $kloters = $operasional->kloters;
            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $request->outlet_id], [
                'stok_mika' => 0, 'stok_dus1' => 0, 'stok_dus2' => 0, 'stok_dus3' => 0, 'stok_box' => 0
            ]);
            $totalMika = $stokOutlet->stok_mika + $kloters->sum('jumlah_mika');
            $totalDus1 = $stokOutlet->stok_dus1 + $kloters->sum('jumlah_dus1');
            $totalDus2 = $stokOutlet->stok_dus2 + $kloters->sum('jumlah_dus2');
            $totalDus3 = $stokOutlet->stok_dus3 + $kloters->sum('jumlah_dus3');
            $totalBox = $stokOutlet->stok_box + $kloters->sum('jumlah_box');

            $tempCatatan = Session::get('catatan_operasionals', []);
            $totalPemasukkan = array_sum(array_column(array_filter($tempCatatan, fn($c) => $c['jenis'] === 'pemasukkan'), 'jumlah'));
            $totalPengeluaran = array_sum(array_column(array_filter($tempCatatan, fn($c) => $c['jenis'] === 'pengeluaran'), 'jumlah'));
            $totalUang = $totalUangPenjualan + $totalPemasukkan - $totalPengeluaran;

            $rekap = Rekap::create([
                'outlet_id' => $request->outlet_id,
                'operasional_id' => $request->operasional_id,
                'pegawai_id' => Session::get('user')->id,
                'total_donat_terjual' => $totalDonatTerjual,
                'sisa_mika' => $totalMika,
                'sisa_dus1' => $totalDus1,
                'sisa_dus2' => $totalDus2,
                'sisa_dus3' => $totalDus3,
                'sisa_box' => $totalBox,
                'total_uang' => $totalUang,
                'tanggal' => Carbon::today()->toDateString(),
                'status' => 'pending',
            ]);

            // Simpan catatan operasional ke database
            foreach ($tempCatatan as $catatan) {
                CatatanOperasional::create([
                    'outlet_id' => $request->outlet_id,
                    'operasional_id' => $request->operasional_id,
                    'pegawai_id' => Session::get('user')->id,
                    'rekap_id' => $rekap->id,
                    'jenis' => $catatan['jenis'],
                    'jumlah' => $catatan['jumlah'],
                    'catatan' => $catatan['catatan'],
                ]);
            }

            Session::forget('catatan_operasionals');
            Session::flash('success', 'Rekap harian berhasil disimpan.');
            return redirect()->route('pegawai.rekap.detail', [$request->outlet_id, $rekap->id]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
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

        $outlet = Outlet::where('id', $outlet_id)->where('pegawai_id', Session::get('user')->id)->firstOrFail();
        $rekap = Rekap::where('id', $rekap_id)
            ->where('outlet_id', $outlet_id)
            ->with('catatanOperasionals')
            ->firstOrFail();

        $totalDonatTerjual = $rekap->total_donat_terjual;
        $totalMika = $rekap->sisa_mika;
        $totalDus1 = $rekap->sisa_dus1;
        $totalDus2 = $rekap->sisa_dus2;
        $totalDus3 = $rekap->sisa_dus3;
        $totalBox = $rekap->sisa_box;
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
            'totalUang',
            'catatanOperasionals'
        ));
    }
}