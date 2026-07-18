<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\ListrikPembayaran;
use App\Models\Operasional;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class ListrikController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $user = Session::get('user');
        $outlet = Outlet::find($user->outlet_id);

        if (!$outlet) {
            Session::flash('error', 'Anda belum ditugaskan ke outlet.');
            return redirect()->route('pegawai.dashboard');
        }

        $belumBayar = Operasional::where('outlet_id', $outlet->id)
            ->whereNull('listrik_pembayaran_id')
            ->where('biaya_listrik', '>', 0)
            ->orderBy('tanggal', 'desc')
            ->get();

        $totalTagihan = $belumBayar->sum('biaya_listrik');
        $jumlahHari = $belumBayar->count();

        $histori = ListrikPembayaran::where('outlet_id', $outlet->id)
            ->with(['pegawai', 'operasionals'])
            ->orderByDesc('dibayar_at')
            ->paginate(10);

        return view('pegawai.listrik', compact(
            'outlet',
            'belumBayar',
            'totalTagihan',
            'jumlahHari',
            'histori'
        ));
    }

    public function bayar(Request $request)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak.');
            return redirect()->route('login');
        }

        $user = Session::get('user');
        $outlet = Outlet::find($user->outlet_id);

        if (!$outlet) {
            Session::flash('error', 'Anda belum ditugaskan ke outlet.');
            return redirect()->route('pegawai.dashboard');
        }

        try {
            DB::transaction(function () use ($user, $outlet) {
                $belumBayar = Operasional::where('outlet_id', $outlet->id)
                    ->whereNull('listrik_pembayaran_id')
                    ->where('biaya_listrik', '>', 0)
                    ->lockForUpdate()
                    ->orderBy('tanggal')
                    ->get();

                if ($belumBayar->isEmpty()) {
                    throw new \RuntimeException('Tidak ada tagihan listrik yang perlu dibayar.');
                }

                $total = (int) $belumBayar->sum('biaya_listrik');
                $jumlahHari = $belumBayar->count();

                $pembayaran = ListrikPembayaran::create([
                    'outlet_id' => $outlet->id,
                    'pegawai_id' => $user->id,
                    'jumlah_hari' => $jumlahHari,
                    'total_nominal' => $total,
                    'dibayar_at' => now(),
                    'keterangan' => 'Pembayaran listrik ' . $jumlahHari . ' hari operasional',
                ]);

                Operasional::whereIn('id', $belumBayar->pluck('id'))
                    ->update(['listrik_pembayaran_id' => $pembayaran->id]);
            });

            Session::flash('success', 'Pembayaran listrik berhasil dicatat.');
        } catch (\RuntimeException $e) {
            Session::flash('error', $e->getMessage());
        } catch (\Exception $e) {
            Session::flash('error', 'Gagal mencatat pembayaran: ' . $e->getMessage());
        }

        return redirect()->route('pegawai.listrik.index');
    }
}
