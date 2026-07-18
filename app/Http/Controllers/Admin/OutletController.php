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

        $outlets = Outlet::with('pegawais')->get();
        $pegawais = Pegawai::where('role', 'pegawai')->get();
        return view('admin.kelola-outlet', compact('outlets', 'pegawais'));
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
                'biaya_listrik_harian' => 'nullable|integer|min:0',
                'pegawai_ids' => 'nullable|array',
                'pegawai_ids.*' => 'exists:pegawais,id',
            ]);

            $outlet = Outlet::create([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'biaya_listrik_harian' => (int) ($request->biaya_listrik_harian ?? 0),
            ]);

            if ($request->pegawai_ids) {
                Pegawai::whereIn('id', $request->pegawai_ids)->update(['outlet_id' => $outlet->id]);
            }

            Session::flash('success', 'Outlet berhasil ditambahkan.');
            return response()->json(['success' => 'Outlet berhasil ditambahkan.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan outlet: ' . $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        try {
            $outlet = Outlet::with('pegawais')->findOrFail($id);
            $data = $outlet->toArray();
            $data['pegawai_ids'] = $outlet->pegawais->pluck('id')->toArray();
            return response()->json($data);
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
                'biaya_listrik_harian' => 'nullable|integer|min:0',
                'pegawai_ids' => 'nullable|array',
                'pegawai_ids.*' => 'exists:pegawais,id',
            ]);

            $outlet->update([
                'nama' => $request->nama,
                'alamat' => $request->alamat,
                'biaya_listrik_harian' => (int) ($request->biaya_listrik_harian ?? 0),
            ]);

            Pegawai::whereIn('id', $request->pegawai_ids ?: [])->update(['outlet_id' => $outlet->id]);
            Pegawai::whereNotIn('id', $request->pegawai_ids ?: [])->where('outlet_id', $outlet->id)->update(['outlet_id' => null]);

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
            Pegawai::where('outlet_id', $outlet->id)->update(['outlet_id' => null]);
            $outlet->delete();

            Session::flash('success', 'Outlet berhasil dihapus.');
            return response()->json(['success' => 'Outlet berhasil dihapus.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus outlet'], 500);
        }
    }
}