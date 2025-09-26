<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Operasional;
use App\Models\Kloter;
use App\Models\StokOutlet;
use App\Models\HistoriStok;
use App\Models\Rekap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class OperasionalController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }
    
        $outlets = Outlet::all();
        return view('admin.operasional', compact('outlets'));
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

            Operasional::create([
                'outlet_id' => $request->outlet_id,
                'tanggal' => Carbon::today()->toDateString(),
                'status' => 'aktif',
                'total_donat_harian' => 0,
            ]);

            Session::flash('success', 'Operasional berhasil dimulai.');
            return redirect()->route('operasional.detail', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
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

        $kloters = $operasional ? Kloter::where('operasional_id', $operasional->id)->get() : collect([]);
        $historiStoks = $operasional ? HistoriStok::where('outlet_id', $outlet_id)
            ->whereDate('created_at', Carbon::today()->toDateString())
            ->latest()
            ->get() : collect([]);

        // Hitung sisa stok kemasan
        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], [
            'stok_mika' => 0,
            'stok_dus1' => 0,
            'stok_dus2' => 0,
            'stok_dus3' => 0,
            'stok_box' => 0
        ]);

        $totalMika = $stokOutlet->stok_mika + ($kloters->sum('jumlah_mika') ?? 0);
        $totalDus1 = $stokOutlet->stok_dus1 + ($kloters->sum('jumlah_dus1') ?? 0);
        $totalDus2 = $stokOutlet->stok_dus2 + ($kloters->sum('jumlah_dus2') ?? 0);
        $totalDus3 = $stokOutlet->stok_dus3 + ($kloters->sum('jumlah_dus3') ?? 0);
        $totalBox = $stokOutlet->stok_box + ($kloters->sum('jumlah_box') ?? 0);

        // Ambil rekap hari ini
        $rekap = $operasional ? Rekap::where('operasional_id', $operasional->id)
            ->where('outlet_id', $outlet_id)
            ->where('tanggal', Carbon::today()->toDateString())
            ->first() : null;

        return view('admin.detail-operasional', compact(
            'outlet',
            'operasional',
            'kloters',
            'historiStoks',
            'totalMika',
            'totalDus1',
            'totalDus2',
            'totalDus3',
            'totalBox',
            'rekap'
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
                'jumlah_donat' => 'required|integer|min:0',
                'jumlah_mika' => 'required|integer|min:0',
                'jumlah_dus1' => 'required|integer|min:0',
                'jumlah_dus2' => 'required|integer|min:0',
                'jumlah_dus3' => 'required|integer|min:0',
                'jumlah_box' => 'required|integer|min:0',
            ]);

            $operasional = Operasional::findOrFail($request->operasional_id);
            if ($operasional->status !== 'aktif') {
                Session::flash('error', 'Operasional sudah selesai atau belum dimulai.');
                return redirect()->back();
            }

            $kloter = Kloter::create([
                'operasional_id' => $request->operasional_id,
                'jumlah_donat' => $request->jumlah_donat,
                'jumlah_mika' => $request->jumlah_mika,
                'jumlah_dus1' => $request->jumlah_dus1,
                'jumlah_dus2' => $request->jumlah_dus2,
                'jumlah_dus3' => $request->jumlah_dus3,
                'jumlah_box' => $request->jumlah_box,
            ]);

            $operasional->total_donat_harian += $request->jumlah_donat;
            $operasional->save();

            // Catat histori stok
            if ($request->jumlah_donat > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'donat',
                    'jumlah_perubahan' => $request->jumlah_donat,
                    'keterangan' => 'Kloter ke-' . $kloter->id,
                ]);
            }
            if ($request->jumlah_mika > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'mika',
                    'jumlah_perubahan' => $request->jumlah_mika,
                    'keterangan' => 'Kloter ke-' . $kloter->id,
                ]);
            }
            if ($request->jumlah_dus1 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'dus1',
                    'jumlah_perubahan' => $request->jumlah_dus1,
                    'keterangan' => 'Kloter ke-' . $kloter->id,
                ]);
            }
            if ($request->jumlah_dus2 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'dus2',
                    'jumlah_perubahan' => $request->jumlah_dus2,
                    'keterangan' => 'Kloter ke-' . $kloter->id,
                ]);
            }
            if ($request->jumlah_dus3 > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'dus3',
                    'jumlah_perubahan' => $request->jumlah_dus3,
                    'keterangan' => 'Kloter ke-' . $kloter->id,
                ]);
            }
            if ($request->jumlah_box > 0) {
                HistoriStok::create([
                    'outlet_id' => $operasional->outlet_id,
                    'jenis_stok' => 'box',
                    'jumlah_perubahan' => $request->jumlah_box,
                    'keterangan' => 'Kloter ke-' . $kloter->id,
                ]);
            }

            Session::flash('success', 'Kloter berhasil ditambahkan.');
            return redirect()->route('operasional.detail', $operasional->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal menambah kloter: ' . $e->getMessage());
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
                'stok_mika' => 'required|integer|min:0',
                'stok_dus1' => 'required|integer|min:0',
                'stok_dus2' => 'required|integer|min:0',
                'stok_dus3' => 'required|integer|min:0',
                'stok_box' => 'required|integer|min:0',
            ]);

            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $request->outlet_id]);
            $oldStok = [
                'mika' => $stokOutlet->stok_mika,
                'dus1' => $stokOutlet->stok_dus1,
                'dus2' => $stokOutlet->stok_dus2,
                'dus3' => $stokOutlet->stok_dus3,
                'box' => $stokOutlet->stok_box,
            ];

            $stokOutlet->update([
                'stok_mika' => $request->stok_mika,
                'stok_dus1' => $request->stok_dus1,
                'stok_dus2' => $request->stok_dus2,
                'stok_dus3' => $request->stok_dus3,
                'stok_box' => $request->stok_box,
            ]);

            // Catat histori stok
            $fields = ['mika' => $request->stok_mika, 'dus1' => $request->stok_dus1, 'dus2' => $request->stok_dus2, 'dus3' => $request->stok_dus3, 'box' => $request->stok_box];
            foreach ($fields as $key => $value) {
                $diff = $value - $oldStok[$key];
                if ($diff != 0) {
                    HistoriStok::create([
                        'outlet_id' => $request->outlet_id,
                        'jenis_stok' => $key,
                        'jumlah_perubahan' => $diff,
                        'keterangan' => 'Update stok manual',
                    ]);
                }
            }

            Session::flash('success', 'Stok berhasil diperbarui.');
            return redirect()->route('operasional.detail', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memperbarui stok: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}