<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kasbon;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KasbonController extends Controller
{
    // Tampilkan halaman kasbon admin dengan daftar pegawai
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $pegawai = Pegawai::where('role', 'pegawai')->get();
        return view('admin.kasbon', compact('pegawai'));
    }

    // Tampilkan pengajuan kasbon pegawai yang dipilih dan histori kasbon
    public function showPegawaiKasbon($pegawaiId)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $pegawai = Pegawai::findOrFail($pegawaiId);
        $pengajuanKasbon = Kasbon::where('pegawai_id', $pegawaiId)->where('status', 'pending')->get();
        $historiKasbon = Kasbon::where('pegawai_id', $pegawaiId)->orderBy('created_at', 'desc')->get();

        return view('admin.kasbon-detail', compact('pegawai', 'pengajuanKasbon', 'historiKasbon'));
    }

    // Setujui pengajuan kasbon
    public function approveKasbon($id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $kasbon = Kasbon::findOrFail($id);
        $kasbon->status = 'approved';
        $kasbon->save();

        return response()->json(['success' => 'Kasbon disetujui']);
    }

    // Tolak pengajuan kasbon
    public function rejectKasbon($id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $kasbon = Kasbon::findOrFail($id);
        $kasbon->status = 'rejected';
        $kasbon->save();

        return response()->json(['success' => 'Kasbon ditolak']);
    }
}
