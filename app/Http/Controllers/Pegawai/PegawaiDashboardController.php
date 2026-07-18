<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Operasional;
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

        $outlet_id = Session::get('user')->outlet_id;
        $outlet = Outlet::where('id', $outlet_id)
            ->with(['operasionals' => function ($query) {
                $query->where('tanggal', Carbon::today()->toDateString());
            }, 'operasionals.rekap'])
            ->first();

        $listrikHariBelumBayar = 0;
        $listrikTotalTagihan = 0;
        if ($outlet) {
            $listrikQuery = Operasional::where('outlet_id', $outlet->id)
                ->whereNull('listrik_pembayaran_id')
                ->where('biaya_listrik', '>', 0);

            $listrikHariBelumBayar = (clone $listrikQuery)->count();
            $listrikTotalTagihan = (int) (clone $listrikQuery)->sum('biaya_listrik');
        }

        return view('pegawai.dashboard', compact(
            'outlet',
            'listrikHariBelumBayar',
            'listrikTotalTagihan'
        ));
    }
}
