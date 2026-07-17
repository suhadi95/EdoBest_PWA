<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Operasional;
use App\Models\Kloter;
use App\Models\StokOutlet;
use App\Models\HistoriStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $outlets = Outlet::all();
        return view('admin.dashboard', compact('outlets'));
    }

    public function operasional($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::findOrFail($outlet_id);
        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first();

        $kloters = $operasional ? $operasional->kloters : collect([]);
        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0, 'stok_dus1' => 0, 'stok_dus2' => 0, 'stok_dus3' => 0, 'stok_box' => 0
        ]);

        return view('admin.operasional', compact('outlet', 'operasional', 'kloters', 'stokOutlet'));
    }

    public function tambahKloter(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'operasional_id' => 'required|exists:operasionals,id',
                'jumlah_donat' => 'required|integer|min:1',
                'stok_mika' => 'required|integer|min:0',
                'stok_dus1' => 'required|integer|min:0',
                'stok_dus2' => 'required|integer|min:0',
                'stok_dus3' => 'required|integer|min:0',
                'stok_box' => 'required|integer|min:0',
                'stok_lilin' => 'nullable|integer|min:0',
            ]);

            $operasional = Operasional::findOrFail($request->operasional_id);
            if ($operasional->status !== 'aktif') {
                return response()->json(['error' => 'Operasional tidak aktif'], 422);
            }

            // Hitung nomor urut kloter untuk hari ini
            $today = Carbon::today()->toDateString();
            $kloterCount = Kloter::where('operasional_id', $request->operasional_id)
                ->whereDate('created_at', $today)
                ->count() + 1;

            // Buat kloter
            $kloter = Kloter::create([
                'operasional_id' => $request->operasional_id,
                'jumlah_donat' => $request->jumlah_donat,
            ]);

            // Update stok outlet
            $stokOutlet = StokOutlet::where('outlet_id', $request->outlet_id)->firstOrFail();
            $stokOutlet->stok_mika += $request->stok_mika;
            $stokOutlet->stok_dus1 += $request->stok_dus1;
            $stokOutlet->stok_dus2 += $request->stok_dus2;
            $stokOutlet->stok_dus3 += $request->stok_dus3;
            $stokOutlet->stok_box += $request->stok_box;
            $stokOutlet->stok_lilin += $request->stok_lilin ?? 0;
            $stokOutlet->save();

            // Catat histori stok
            $stokChanges = [
                'mika' => $request->stok_mika,
                'dus1' => $request->stok_dus1,
                'dus2' => $request->stok_dus2,
                'dus3' => $request->stok_dus3,
                'box' => $request->stok_box,
                'lilin' => $request->stok_lilin ?? 0,
            ];

            foreach ($stokChanges as $jenis => $jumlah) {
                if ($jumlah > 0) {
                    HistoriStok::create([
                        'outlet_id' => $request->outlet_id,
                        'jenis_stok' => $jenis,
                        'jumlah_perubahan' => $jumlah,
                        'keterangan' => "Kloter ke-{$kloterCount} ({$today})",
                    ]);
                }
            }

            Session::flash('success', 'Kloter berhasil ditambahkan.');
            return redirect()->route('admin.operasional', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menambah kloter: ' . $e->getMessage());
            return redirect()->back();
        }
    }

    public function stok($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::findOrFail($outlet_id);
        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0,
            'stok_dus1' => 0,
            'stok_dus2' => 0,
            'stok_dus3' => 0,
            'stok_box' => 0,
            'stok_lilin' => 0
        ]);
        $historiStoks = HistoriStok::where('outlet_id', $outlet_id)
            ->latest()
            ->paginate(20);

        return view('admin.detail-stok', compact('outlet', 'stokOutlet', 'historiStoks'));
    }

    public function updateStok(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'jenis_stok' => 'required|in:mika,dus1,dus2,dus3,box,lilin',
                'jumlah_perubahan' => 'required|integer',
                'keterangan' => 'required|string|max:255',
            ]);

            $stokOutlet = StokOutlet::where('outlet_id', $request->outlet_id)->firstOrFail();
            $today = Carbon::today()->toDateString();
            $manualUpdateCount = HistoriStok::where('outlet_id', $request->outlet_id)
                ->where('keterangan', 'like', 'Update Manual%')
                ->whereDate('created_at', $today)
                ->count() + 1;

            // Update stok berdasarkan jenis
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
                case 'lilin':
                    $stokOutlet->stok_lilin += $request->jumlah_perubahan;
                    break;
            }

            // Validasi stok tidak negatif
            if ($stokOutlet->stok_mika < 0 || $stokOutlet->stok_dus1 < 0 || 
                $stokOutlet->stok_dus2 < 0 || $stokOutlet->stok_dus3 < 0 || 
                $stokOutlet->stok_box < 0 || $stokOutlet->stok_lilin < 0) {
                return response()->json(['error' => 'Stok tidak boleh negatif'], 422);
            }

            $stokOutlet->save();

            // Catat histori stok
            HistoriStok::create([
                'outlet_id' => $request->outlet_id,
                'jenis_stok' => $request->jenis_stok,
                'jumlah_perubahan' => $request->jumlah_perubahan,
                'keterangan' => "Update Manual {$manualUpdateCount} ({$today}): {$request->keterangan}",
            ]);

            Session::flash('success', 'Stok berhasil diperbarui.');
            return redirect()->route('admin.stok', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memperbarui stok: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}