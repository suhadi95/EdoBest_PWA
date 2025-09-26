<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Outlet;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class OutletController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $outlets = Outlet::with('pegawai')->get();
        $pegawai = Pegawai::where('role', 'pegawai')->get();
        return view('admin.kelola-outlet', compact('outlets', 'pegawai'));
    }

    public function store(Request $request)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'nama' => 'required|string|max:255|unique:outlets,nama',
                'alamat' => 'required|string',
                'pegawai_id' => 'nullable|exists:pegawai,id',
            ]);

            Outlet::create($request->all());

            Session::flash('success', 'Outlet berhasil ditambahkan.');
            return response()->json(['success' => 'Outlet berhasil ditambahkan.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan outlet'], 500);
        }
    }

    public function edit($id)
    {
        try {
            $outlet = Outlet::findOrFail($id);
            return response()->json($outlet);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data outlet tidak ditemukan'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $outlet = Outlet::findOrFail($id);

            $request->validate([
                'nama' => 'required|string|max:255|unique:outlets,nama,' . $id,
                'alamat' => 'required|string',
                'pegawai_id' => 'nullable|exists:pegawai,id',
            ]);

            $outlet->update($request->all());

            Session::flash('success', 'Outlet berhasil diupdate.');
            return response()->json(['success' => 'Outlet berhasil diupdate.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengupdate outlet: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $outlet = Outlet::findOrFail($id);
            $outlet->delete();

            Session::flash('success', 'Outlet berhasil dihapus.');
            return response()->json(['success' => 'Outlet berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus outlet'], 500);
        }
    }
}