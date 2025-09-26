<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\StokOutlet;
use App\Models\HistoriStok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class StokKemasanController extends Controller
{
    // Tampilkan daftar outlet untuk pilih stok kemasan
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $outlets = Outlet::all();
        return view('admin.kelola-stok-kemasan', compact('outlets'));
    }

    // Tampilkan detail stok kemasan outlet
    public function detail($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::findOrFail($outlet_id);
        $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $outlet_id], ['stok_mika' => 0, 'stok_dus1' => 0, 'stok_dus2' => 0, 'stok_dus3' => 0, 'stok_box' => 0]);
        $historiStoks = $outlet->historiStoks()->latest()->get();

        return view('admin.detail-stok', compact('outlet', 'stokOutlet', 'historiStoks'));
    }

    // Update stok manual
    public function updateStok(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'outlet_id' => 'required|exists:outlets,id',
                'jenis_stok' => 'required|in:mika,dus1,dus2,dus3,box',
                'jumlah_perubahan' => 'required|integer', // Positif untuk tambah, negatif untuk kurang
                'keterangan' => 'required|string',
            ]);

            $stokOutlet = StokOutlet::firstOrCreate(['outlet_id' => $request->outlet_id]);

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
            }
            $stokOutlet->save();

            // Catat histori
            $this->catatHistori($request->outlet_id, $request->jenis_stok, $request->jumlah_perubahan, $request->keterangan);

            Session::flash('success', 'Stok berhasil diupdate.');
            return redirect()->route('stok.detail', $request->outlet_id);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Session::flash('error', 'Validasi gagal: ' . implode(', ', $e->errors()));
            return redirect()->back();
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal mengupdate stok.');
            return redirect()->back();
        }
    }

    private function catatHistori($outlet_id, $jenis_stok, $jumlah_perubahan, $keterangan)
    {
        if ($jumlah_perubahan != 0) {
            HistoriStok::create([
                'outlet_id' => $outlet_id,
                'jenis_stok' => $jenis_stok,
                'jumlah_perubahan' => $jumlah_perubahan,
                'keterangan' => $keterangan,
            ]);
        }
    }
}