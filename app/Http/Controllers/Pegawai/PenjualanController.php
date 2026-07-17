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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function penjualan($outlet_id)
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
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$operasional) {
            Session::flash('error', 'Operasional belum dimulai hari ini.');
            return redirect()->route('pegawai.dashboard');
        }

        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0,
            'stok_dus1' => 0,
            'stok_dus2' => 0,
            'stok_dus3' => 0,
            'stok_box' => 0
        ]);

        $kloters = Kloter::where('operasional_id', $operasional->id)
            ->orderBy('created_at')
            ->get();

        $totalKloter = $kloters->count();
        $totalDonat = $kloters->sum('jumlah_donat');

        $transaksis = Transaksi::where('operasional_id', $operasional->id)
            ->where('outlet_id', $outlet_id)
            ->with('items')
            ->latest()
            ->get();

        $rekap = $operasional->rekap()->with(['catatanOperasionals', 'operasional.transaksis.items'])->first() ?? null;

        return view('pegawai.penjualan', compact('outlet', 'operasional', 'totalKloter', 'totalDonat', 'stokOutlet', 'transaksis', 'rekap', 'kloters'));
    }

    public function transaksi($outlet_id)
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
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        if (!$operasional) {
            Session::flash('error', 'Operasional belum dimulai hari ini.');
            return redirect()->route('pegawai.dashboard');
        }

        // Hitung nomor transaksi berdasarkan nomor terbesar + 1 (reset setiap hari)
        $maxNoTransaksi = Transaksi::whereHas('operasional', function ($query) use ($operasional) {
            $query->where('tanggal', $operasional->tanggal)
                ->where('outlet_id', $operasional->outlet_id);
        })->max('no_transaksi') ?? 0;

        $transaksiCount = $maxNoTransaksi + 1;
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
                'kemasan' => 'required|in:mika,dus1,dus2,dus3,box,box12,lilin',
                'jumlah' => 'required|integer|min:1',
                'tipe' => 'nullable|in:reguler,classic,custom',
            ]);

            $donatPerItem = [
                'mika' => 1,
                'dus1' => 1,
                'dus2' => 2,
                'dus3' => 3,
                'box' => 6,
                'box12' => 12,
                'lilin' => 0, // Lilin tidak menggunakan donat
            ];

            $hargaItem = HargaItem::where('nama_item', $request->kemasan)->firstOrFail();

            // Untuk lilin, tidak ada pilihan tipe, langsung pakai harga reguler
            if ($request->kemasan === 'lilin') {
                $harga = $hargaItem->harga_reguler;
                $tipe = 'reguler'; // Default tipe untuk lilin
            } else {
                $tipe = $request->tipe;
                $harga = $request->tipe === 'reguler' ? $hargaItem->harga_reguler : ($request->tipe === 'classic' ? $hargaItem->harga_classic : ($hargaItem->harga_costum ?? 0));
            }

            $totalHarga = $request->jumlah * $harga;
            $donat = $request->jumlah * $donatPerItem[$request->kemasan];

            $item = [
                'kemasan' => $request->kemasan,
                'jumlah' => $request->jumlah,
                'tipe' => $tipe,
                'harga' => $harga,
                'subtotal' => $totalHarga,
            ];

            $tempItems = Session::get('transaksi_items', []);
            $wasEmpty = empty($tempItems);
            $tempItems[] = $item;
            Session::put('transaksi_items', $tempItems);

            return response()->json([
                'success' => 'Item berhasil ditambahkan',
                'reload' => $wasEmpty,
                'item' => $item
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan item: ' . $e->getMessage()], 500);
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
                return response()->json([
                    'success' => 'Item berhasil dihapus',
                    'reload' => true
                ]);
            }
            return response()->json(['error' => 'Item tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus item'], 500);
        }
    }

    public function hapusSemuaItem(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            Session::forget('transaksi_items');
            return response()->json([
                'success' => 'Semua item berhasil dihapus',
                'reload' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus semua item'], 500);
        }
    }

    public function simpanTransaksi(Request $request)
    {
        try {
            Log::info('Simpan transaksi request', [
                'request_data' => $request->all(),
                'session_user' => Session::get('user'),
                'temp_items' => Session::get('transaksi_items', [])
            ]);

            if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'operasional_id' => 'required|exists:operasionals,id',
                'metode_pembayaran' => 'required|in:tunai,qris,transfer,grabfood,gofood',
            ]);

            $tempItems = Session::get('transaksi_items', []);
            if (empty($tempItems)) {
                Session::flash('error', 'Tidak ada item transaksi.');
                return redirect()->back();
            }

            $donatPerItem = [
                'mika' => 1,
                'dus1' => 1,
                'dus2' => 2,
                'dus3' => 3,
                'box' => 6,
                'box12' => 12,
                'lilin' => 0, // Lilin tidak menggunakan donat
            ];

            $operasional = Operasional::findOrFail($request->operasional_id);
            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $request->outlet_id], [
                'stok_mika' => 0,
                'stok_dus1' => 0,
                'stok_dus2' => 0,
                'stok_dus3' => 0,
                'stok_box' => 0,
                'stok_box12' => 0,
                'stok_lilin' => 0,
            ]);

            Log::info('Operasional data', [
                'operasional_id' => $operasional->id,
                'total_donat_harian' => $operasional->total_donat_harian,
                'outlet_id' => $operasional->outlet_id
            ]);

            $totalDonatTersedia = $operasional->total_donat_harian ?? 0;
            $totalDonatTransaksi = array_sum(array_map(function ($item) use ($donatPerItem) {
                return $item['jumlah'] * ($donatPerItem[$item['kemasan']] ?? 0);
            }, $tempItems));

            // Validasi stok donat
            if ($totalDonatTransaksi > $totalDonatTersedia) {
                Session::flash('error', 'Stok donat tidak cukup. Tersedia: ' . $totalDonatTersedia . ' donat.');
                return redirect()->back();
            }

            // Validasi stok kemasan (termasuk lilin)
            $stokTersedia = [
                'mika' => $stokOutlet->stok_mika,
                'dus1' => $stokOutlet->stok_dus1,
                'dus2' => $stokOutlet->stok_dus2,
                'dus3' => $stokOutlet->stok_dus3,
                'box' => $stokOutlet->stok_box,
                'box12' => $stokOutlet->stok_box12,
                'lilin' => $stokOutlet->stok_lilin,
            ];

            foreach ($tempItems as $item) {
                if ($item['jumlah'] > $stokTersedia[$item['kemasan']]) {
                    $namaItem = $item['kemasan'] === 'lilin' ? 'Lilin' : ucfirst($item['kemasan']);
                    Session::flash('error', 'Stok ' . $namaItem . ' tidak cukup. Tersedia: ' . $stokTersedia[$item['kemasan']] . '.');
                    return redirect()->back();
                }
            }

            // Hitung nomor transaksi untuk hari ini (nomor terbesar + 1)
            $operasional = Operasional::findOrFail($request->operasional_id);
            $maxNoTransaksi = Transaksi::whereHas('operasional', function ($query) use ($operasional) {
                $query->where('tanggal', $operasional->tanggal)
                    ->where('outlet_id', $operasional->outlet_id);
            })->max('no_transaksi') ?? 0;

            $noTransaksi = $maxNoTransaksi + 1;

            // Validasi nomor transaksi
            if ($noTransaksi <= 0) {
                $noTransaksi = 1; // Default ke 1 jika tidak ada transaksi sebelumnya
            }

            Log::info('Transaction number calculation', [
                'operasional_id' => $request->operasional_id,
                'max_no_transaksi' => $maxNoTransaksi,
                'new_no_transaksi' => $noTransaksi
            ]);

            // Simpan transaksi
            $transaksi = Transaksi::create([
                'outlet_id' => $request->outlet_id,
                'operasional_id' => $request->operasional_id,
                'pegawai_id' => Session::get('user')->id,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_donat' => $totalDonatTransaksi,
                'total_harga' => array_sum(array_column($tempItems, 'subtotal')),
                'no_transaksi' => $noTransaksi,
            ]);

            // Simpan item transaksi dan kurangi stok
            foreach ($tempItems as $item) {
                TransaksiItem::create([
                    'transaksi_id' => $transaksi->id,
                    'kemasan' => $item['kemasan'],
                    'jumlah' => $item['jumlah'],
                    'tipe' => $item['tipe'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Kurangi stok kemasan (termasuk lilin)
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
                    case 'box12':
                        $stokOutlet->stok_box12 -= $item['jumlah'];
                        break;
                    case 'lilin':
                        $stokOutlet->stok_lilin -= $item['jumlah'];
                        break;
                }

                // Catat histori stok kemasan (termasuk lilin)
                HistoriStok::create([
                    'outlet_id' => $request->outlet_id,
                    'jenis_stok' => $item['kemasan'],
                    'jumlah_perubahan' => -$item['jumlah'],
                    'keterangan' => 'Transaksi ke-' . ($transaksi->no_transaksi ?? $transaksi->id),
                ]);
            }

            // Kurangi stok donat
            $operasional->total_donat_harian -= $totalDonatTransaksi;
            $operasional->save();

            $stokOutlet->save();

            Session::forget('transaksi_items');
            Session::flash('success', 'Transaksi berhasil disimpan.');
            return redirect()->route('pegawai.penjualan', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->validator->errors()->all()));
            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Error saving transaction: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
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

        $pegawai = \App\Models\Pegawai::find(Session::get('user')->id);
        if (!$pegawai || $pegawai->outlet_id != $outlet_id) {
            abort(403, 'Akses ditolak.');
        }
        $outlet = Outlet::findOrFail($outlet_id);
        $transaksi = Transaksi::where('id', $transaksi_id)
            ->where('outlet_id', $outlet_id)
            ->with('items')
            ->firstOrFail();
        $operasional = Operasional::findOrFail($transaksi->operasional_id);

        return view('pegawai.transaksi', compact('outlet', 'transaksi', 'operasional'));
    }

    public function deleteTransaksi($outlet_id, $transaksi_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $pegawai = \App\Models\Pegawai::find(Session::get('user')->id);
        if (!$pegawai || $pegawai->outlet_id != $outlet_id) {
            abort(403, 'Akses ditolak.');
        }

        $transaksi = Transaksi::where('id', $transaksi_id)
            ->where('outlet_id', $outlet_id)
            ->first();

        if (!$transaksi) {
            Session::flash('error', 'Transaksi tidak ditemukan.');
            return redirect()->back();
        }

        try {
            DB::transaction(function () use ($transaksi) {
                // Muat relasi items
                $transaksi->load('items');

                // Ambil operasional dan stok outlet terkait
                $operasional = Operasional::findOrFail($transaksi->operasional_id);
                $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $transaksi->outlet_id], [
                    'stok_mika' => 0,
                    'stok_dus1' => 0,
                    'stok_dus2' => 0,
                    'stok_dus3' => 0,
                    'stok_box' => 0,
                    'stok_box12' => 0,
                ]);

                // Kembalikan stok kemasan berdasarkan item transaksi
                foreach ($transaksi->items as $item) {
                    switch ($item->kemasan) {
                        case 'mika':
                            $stokOutlet->stok_mika += $item->jumlah;
                            break;
                        case 'dus1':
                            $stokOutlet->stok_dus1 += $item->jumlah;
                            break;
                        case 'dus2':
                            $stokOutlet->stok_dus2 += $item->jumlah;
                            break;
                        case 'dus3':
                            $stokOutlet->stok_dus3 += $item->jumlah;
                            break;
                        case 'box':
                            $stokOutlet->stok_box += $item->jumlah;
                            break;
                        case 'box12':
                            $stokOutlet->stok_box12 += $item->jumlah;
                            break;
                    }

                    // Catat histori stok pengembalian
                    HistoriStok::create([
                        'outlet_id' => $transaksi->outlet_id,
                        'jenis_stok' => $item->kemasan,
                        'jumlah_perubahan' => $item->jumlah, // kembalikan stok
                        'keterangan' => 'Reversal Transaksi ke-' . ($transaksi->no_transaksi ?? $transaksi->id),
                    ]);
                }

                // Kembalikan stok donat harian
                $operasional->total_donat_harian += ($transaksi->total_donat ?? 0);

                // Simpan perubahan stok & operasional
                $stokOutlet->save();
                $operasional->save();

                // Hapus item transaksi terkait dan transaksi itu sendiri
                $transaksi->items()->delete();
                $transaksi->delete();
            });

            Session::flash('success', 'Transaksi berhasil dihapus dan stok dikembalikan.');
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
            return redirect()->back();
        }
    }

}
