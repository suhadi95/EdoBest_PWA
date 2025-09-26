<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Operasional;
use App\Models\Rekap;
use App\Models\Kloter;
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
        $operasionals = Operasional::where('outlet_id', $outlet_id)
            ->with('rekap.catatanOperasionals')
            ->latest()
            ->get();

        return view('admin.operasional', compact('outlet', 'operasionals'));
    }

    public function validasiOperasional(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                Session::flash('error', 'Akses ditolak.');
                return redirect()->route('login');
            }

            $request->validate([
                'operasional_id' => 'required|exists:operasionals,id',
                'outlet_id' => 'required|exists:outlets,id',
            ]);

            $operasional = Operasional::findOrFail($request->operasional_id);
            if ($operasional->status === 'selesai') {
                Session::flash('error', 'Operasional sudah divalidasi.');
                return redirect()->back();
            }

            if (!$operasional->rekap) {
                Session::flash('error', 'Rekap harian belum dibuat oleh pegawai.');
                return redirect()->back();
            }

            $operasional->status = 'selesai';
            $operasional->save();

            Session::flash('success', 'Operasional berhasil divalidasi.');
            return redirect()->route('admin.operasional', $request->outlet_id);
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal memvalidasi operasional: ' . $e->getMessage());
            return redirect()->back();
        }
    }
}