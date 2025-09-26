<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Pegawai;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Validasi input
        $request->validate([
            'username' => 'required|string',
        ]);

        // Cari pegawai berdasarkan username
        $pegawai = Pegawai::where('username', $request->username)->first();

        if (!$pegawai) {
            return redirect()->back()->with('error', 'Username tidak ditemukan.');
        }

        // Simpan data pengguna ke session
        Session::put('user', $pegawai);

        // Redirect berdasarkan role
        if ($pegawai->role === 'admin') {
            return redirect()->route('admin.dashboard');
        } elseif ($pegawai->role === 'pegawai') {
            return redirect()->route('pegawai.dashboard');
        }

        // Jika role tidak valid, hapus session dan kembalikan error
        Session::forget('user');
        return redirect()->back()->with('error', 'Role pengguna tidak valid.');
    }

    public function logout()
    {
        Session::forget('user');
        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}