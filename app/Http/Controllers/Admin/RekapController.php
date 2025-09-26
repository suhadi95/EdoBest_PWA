<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Rekap;
use App\Models\Operasional;
use App\Models\Kloter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RekapController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlets = Outlet::all();
        return view('admin.rekap', compact('outlets'));
    }

    public function detail($outlet_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $outlet = Outlet::findOrFail($outlet_id);
        $rekaps = Rekap::where('outlet_id', $outlet_id)
            ->where('status', 'validated')
            ->with('catatanOperasionals')
            ->latest()
            ->get();

        $operasional = Operasional::where('outlet_id', $outlet_id)
            ->where('tanggal', \Carbon\Carbon::today()->toDateString())
            ->first();

        $rekapHariIni = Rekap::where('outlet_id', $outlet_id)
            ->where('tanggal', \Carbon\Carbon::today()->toDateString())
            ->with('catatanOperasionals')
            ->first();

        // Ambil kloter untuk setiap rekap
        $kloters = [];
        if ($rekapHariIni) {
            $kloters[$rekapHariIni->id] = Kloter::where('operasional_id', $rekapHariIni->operasional_id)->get();
        }
        foreach ($rekaps as $rekap) {
            $kloters[$rekap->id] = Kloter::where('operasional_id', $rekap->operasional_id)->get();
        }

        return view('admin.rekap-detail', compact('outlet', 'rekaps', 'operasional', 'rekapHariIni', 'kloters'));
    }

    public function validasiRekap(Request $request, $outlet_id, $rekap_id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $rekap = Rekap::where('id', $rekap_id)
                ->where('outlet_id', $outlet_id)
                ->firstOrFail();

            if ($rekap->status !== 'pending') {
                Session::flash('error', 'Rekap sudah divalidasi.');
                return redirect()->back();
            }

            $operasional = Operasional::findOrFail($rekap->operasional_id);
            if ($operasional->status !== 'aktif') {
                Session::flash('error', 'Operasional sudah ditutup atau belum dimulai.');
                return redirect()->back();
            }

            // Ubah status rekap menjadi validated
            $rekap->status = 'validated';
            $rekap->save();

            // Ubah status operasional menjadi tutup
            $operasional->status = 'tutup';
            $operasional->save();

            Session::flash('success', 'Rekap harian berhasil divalidasi dan operasional ditutup.');
            return redirect()->route('admin.rekap.detail', $outlet_id);
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memvalidasi rekap: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}