<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\Kasbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class KasbonController extends Controller
{
    // Tampilkan form pengajuan kasbon
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        // Ambil histori pengajuan kasbon pegawai
        $kasbonHistori = Kasbon::where('pegawai_id', Session::get('user')->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('pegawai.kasbon', compact('kasbonHistori'));
    }

    // Simpan pengajuan kasbon
    public function store(Request $request)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $request->validate([
            'nominal' => 'required|integer|min:1',
            'keterangan' => 'required|string|max:255',
        ]);

        // Cek apakah pegawai sudah mengajukan kasbon hari ini
        $today = now()->toDateString();
        $existingKasbon = Kasbon::where('pegawai_id', Session::get('user')->id)
            ->whereDate('tanggal', $today)
            ->first();

        if ($existingKasbon) {
            Session::flash('error', 'Anda sudah mengajukan kasbon hari ini. Silakan tunggu hingga besok untuk mengajukan kasbon lagi.');
            return redirect()->route('pegawai.kasbon.index');
        }

        try {
            Kasbon::create([
                'pegawai_id' => Session::get('user')->id,
                'tanggal' => now(),
                'nominal' => $request->nominal,
                'keterangan' => $request->keterangan,
                'status' => 'pending',
            ]);

            Session::flash('success', 'Pengajuan kasbon berhasil dikirim.');
        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            Session::flash('error', 'Anda sudah mengajukan kasbon hari ini. Silakan tunggu hingga besok untuk mengajukan kasbon lagi.');
        } catch (\Exception $e) {
            Session::flash('error', 'Terjadi kesalahan saat mengirim pengajuan kasbon. Silakan coba lagi.');
        }

        return redirect()->route('pegawai.kasbon.index');
    }

    // Hapus pengajuan kasbon
    public function destroy($id)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $kasbon = Kasbon::where('id', $id)
            ->where('pegawai_id', Session::get('user')->id)
            ->where('status', 'pending')
            ->first();

        if (!$kasbon) {
            Session::flash('error', 'Pengajuan kasbon tidak ditemukan atau sudah diproses.');
            return redirect()->route('pegawai.kasbon.index');
        }

        $kasbon->delete();

        Session::flash('success', 'Pengajuan kasbon berhasil dihapus.');
        return redirect()->route('pegawai.kasbon.index');
    }
}
