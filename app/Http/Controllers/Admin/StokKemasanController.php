<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\StokOutlet;
use App\Models\HistoriStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class StokKemasanController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $outlets = Outlet::all();
        return view('admin.kelola-stok-kemasan', compact('outlets'));
    }

    public function detail($outlet_id)
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
            'stok_box12' => 0,
            'stok_lilin' => 0
        ]);
        $historiStoks = HistoriStok::where('outlet_id', $outlet_id)
            ->whereIn('jenis_stok', ['mika', 'dus1', 'dus2', 'dus3', 'box', 'box12', 'lilin'])
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
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()->all()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal mengupdate stok: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}