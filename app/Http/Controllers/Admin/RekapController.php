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
        $rekapsPending = Rekap::where('outlet_id', $outlet_id)
            ->where('status', 'pending')
            ->with('catatanOperasionals', 'operasional.transaksis.items')
            ->latest()
            ->get();

        // Rekap validated untuk pagination di tabel
        $rekapsValidated = Rekap::where('outlet_id', $outlet_id)
            ->where('status', 'validated')
            ->with('catatanOperasionals', 'operasional.transaksis.items')
            ->latest()
            ->paginate(20);

        // Semua rekap validated untuk modal (tanpa pagination)
        $allRekapsValidated = Rekap::where('outlet_id', $outlet_id)
            ->where('status', 'validated')
            ->with('catatanOperasionals', 'operasional.transaksis.items')
            ->latest()
            ->get();

        // Ambil kloter untuk setiap rekap
        $kloters = [];
        $totalDonatKloter = [];
        foreach ($rekapsPending as $rekap) {
            $kloterData = Kloter::where('operasional_id', $rekap->operasional_id)->get();
            $kloters[$rekap->id] = $kloterData;
            $totalDonatKloter[$rekap->id] = $kloterData->sum('jumlah_donat');
        }
        foreach ($allRekapsValidated as $rekap) {
            $kloterData = Kloter::where('operasional_id', $rekap->operasional_id)->get();
            $kloters[$rekap->id] = $kloterData;
            $totalDonatKloter[$rekap->id] = $kloterData->sum('jumlah_donat');
        }

        return view('admin.rekap-detail', compact('outlet', 'rekapsPending', 'rekapsValidated', 'allRekapsValidated', 'kloters', 'totalDonatKloter'));
    }

    public function validasiRekap(Request $request, $outlet_id, $rekap_id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Akses ditolak'], 403);
                }
                Session::flash('error', 'Akses ditolak.');
                return redirect()->route('login');
            }

            $rekap = Rekap::where('id', $rekap_id)
                ->where('outlet_id', $outlet_id)
                ->firstOrFail();

            if ($rekap->status !== 'pending') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Rekap sudah divalidasi.'], 400);
                }
                Session::flash('error', 'Rekap sudah divalidasi.');
                return redirect()->back();
            }

            $operasional = Operasional::findOrFail($rekap->operasional_id);
            if ($operasional->status !== 'aktif') {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json(['error' => 'Operasional sudah ditutup atau belum dimulai.'], 400);
                }
                Session::flash('error', 'Operasional sudah ditutup atau belum dimulai.');
                return redirect()->back();
            }

            // Ubah status rekap menjadi validated
            $rekap->status = 'validated';
            $rekap->save();

            // Ubah status operasional menjadi selesai
            $operasional->status = 'selesai';
            $operasional->save();

            // Return JSON response if AJAX request, otherwise redirect
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['success' => true, 'message' => 'Operasional Sudah Divalidasi.']);
            } else {
                Session::flash('success', 'Operasional Sudah Divalidasi.');
                return redirect()->back();
            }
        } catch (\Exception $e) {
            // Return JSON error response to frontend
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['error' => 'Gagal memvalidasi rekap: ' . $e->getMessage()], 500);
            } else {
                Session::flash('error', 'Gagal memvalidasi rekap: ' . $e->getMessage());
                return redirect()->back();
            }
        }
    }
}