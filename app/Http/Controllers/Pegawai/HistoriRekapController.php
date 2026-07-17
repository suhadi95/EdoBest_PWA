<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Rekap;
use App\Models\Outlet;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class HistoriRekapController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $outlet_id = Session::get('user')->outlet_id;
        $outlet = Outlet::findOrFail($outlet_id);

        // Ambil histori rekap (pending dan validated) untuk outlet pegawai
        $historiRekap = Rekap::where('outlet_id', $outlet_id)
            ->whereIn('status', ['pending', 'validated'])
            ->with(['operasional', 'catatanOperasionals'])
            ->orderBy('tanggal', 'desc')
            ->paginate(10);

        return view('pegawai.histori-rekap.index', compact('outlet', 'historiRekap'));
    }

    public function detail($rekap_id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $outlet_id = Session::get('user')->outlet_id;
        
        // Pastikan rekap milik outlet pegawai (pending atau validated)
        $rekap = Rekap::where('id', $rekap_id)
            ->where('outlet_id', $outlet_id)
            ->whereIn('status', ['pending', 'validated'])
            ->with(['operasional.transaksis.items', 'catatanOperasionals', 'operasional.kloters'])
            ->firstOrFail();

        $outlet = Outlet::findOrFail($outlet_id);

        return view('pegawai.histori-rekap.detail', compact('outlet', 'rekap'));
    }
}
