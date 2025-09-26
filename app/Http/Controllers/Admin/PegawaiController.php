<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class PegawaiController extends Controller
{
    // Tampilkan daftar pegawai
    public function index()
    {
        // Cek apakah user adalah admin
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $pegawai = Pegawai::all(); // Ambil semua pegawai
        return view('admin.kelola-pegawai', compact('pegawai'));
    }

    // Tampilkan form tambah pegawai (via modal, tapi kita handle di view)
    public function create()
    {
        // Tidak perlu method terpisah, karena form di modal index
        return redirect()->route('pegawai.index');
    }

    // Simpan pegawai baru
    public function store(Request $request)
    {
        // Cek admin
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:pegawai,username',
            'gaji_harian' => 'required|integer|min:0',
            'bonus_nominal' => 'required|integer|min:0',
            'bonus_syarat' => 'required|integer|min:0',
            'role' => 'required|in:pegawai', // Hanya pegawai, admin manual
        ]);

        Pegawai::create($request->all());

        Session::flash('success', 'Pegawai berhasil ditambahkan2.');
        return response()->json(['success' => 'Pegawai berhasil ditambahkan.']);
    }

    // Update pegawai
    public function update(Request $request, $id)
    {
        // Cek admin
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $pegawai = Pegawai::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:pegawai,username,' . $id,
            'gaji_harian' => 'required|integer|min:0',
            'bonus_nominal' => 'required|integer|min:0',
            'bonus_syarat' => 'required|integer|min:0',
            'role' => 'required|in:pegawai',
        ]);

        $pegawai->update($request->all());

        Session::flash('success', 'Pegawai berhasil diupdate.');
        return response()->json(['success' => 'Pegawai berhasil diupdate.']);
    }

    public function edit($id)
    {
        try {
            $pegawai = Pegawai::findOrFail($id);
            return response()->json($pegawai);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data pegawai tidak ditemukan'], 404);
        }
    }

    public function destroy($id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $pegawai = Pegawai::findOrFail($id);
            $pegawai->delete();

            Session::flash('success', 'Pegawai berhasil dihapus.');
            return response()->json(['success' => 'Pegawai berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus pegawai'], 500);
        }
    }
}
