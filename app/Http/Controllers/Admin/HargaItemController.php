<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HargaItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HargaItemController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        $hargaItems = HargaItem::all();
        return view('admin.kelola-harga', compact('hargaItems'));
    }

    public function edit($id)
    {
        try {
            $hargaItem = HargaItem::findOrFail($id);
            return response()->json($hargaItem);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Data harga tidak ditemukan'], 404);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'harga_reguler' => 'required|numeric|min:0',
                'harga_classic' => 'required|numeric|min:0',
                'harga_costum' => 'required|numeric|min:0',
            ]);

            $hargaItem = HargaItem::findOrFail($id);
            $hargaItem->update([
                'harga_reguler' => $request->harga_reguler,
                'harga_classic' => $request->harga_classic,
                'harga_costum' => $request->harga_costum,
            ]);

            Session::flash('success', 'Harga berhasil diupdate.');
            return response()->json(['success' => 'Harga berhasil diupdate.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengupdate harga'], 500);
        }
    }
}