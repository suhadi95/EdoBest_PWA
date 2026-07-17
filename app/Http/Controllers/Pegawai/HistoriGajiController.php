<?php

namespace App\Http\Controllers\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\GajiHistori;
use App\Models\Kasbon;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class HistoriGajiController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $pegawaiId = Session::get('user')->id;

        $gajiHistori = GajiHistori::where('pegawai_id', $pegawaiId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('pegawai.histori-gaji', compact('gajiHistori'));
    }

    public function detail($gajiId)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'pegawai') {
            Session::flash('error', 'Akses ditolak. Hanya pegawai yang bisa mengakses.');
            return redirect()->route('login');
        }

        $pegawaiId = Session::get('user')->id;
        
        $pegawai = \App\Models\Pegawai::findOrFail($pegawaiId);
        $gaji = GajiHistori::where('id', $gajiId)
            ->where('pegawai_id', $pegawaiId)
            ->first();

        if (!$gaji) {
            return response()->json(['error' => 'Gaji tidak ditemukan'], 404);
        }

        // Hitung kalkulasi gaji untuk periode dari tanggal gaji
        if ($gaji->periode_mulai && $gaji->periode_akhir) {
            // Gunakan periode yang tersimpan
            $kalkulasiGaji = $this->calculateGajiFlexible($pegawaiId, $gaji->periode_mulai, $gaji->periode_akhir);
        } else {
            // Fallback ke bulan/tahun untuk backward compatibility
            $bulan = $gaji->tanggal_gaji->month;
            $tahun = $gaji->tanggal_gaji->year;
            $kalkulasiGaji = $this->calculateGaji($pegawaiId, $bulan, $tahun);
        }

        // Jika rincian gaji harian sudah tersimpan di database, gunakan itu
        if ($gaji->rincian_gaji_harian && count($gaji->rincian_gaji_harian) > 0) {
            $kalkulasiGaji['rincian'] = $gaji->rincian_gaji_harian;
            // Hitung total gaji dari rincian yang tersimpan
            $totalGaji = 0;
            foreach ($gaji->rincian_gaji_harian as $rincian) {
                $totalGaji += $rincian['gaji'] + $rincian['bonus'];
            }
            $kalkulasiGaji['total'] = $totalGaji;
        }

        // Ambil kasbon yang belum dibayar pada bulan tersebut (untuk histori, gunakan data pada saat validasi)
        // Karena kasbon sudah diupdate menjadi lunas saat validasi, kita gunakan total dari field kasbon
        // Untuk detail per item, ambil kasbon yang dibuat sebelum tanggal gaji dan belum dibayar
        $kasbonBelumDibayar = Kasbon::where('pegawai_id', $pegawaiId)
            ->where('status', 'approved')
            ->where('tanggal', '<=', $gaji->tanggal_gaji)
            ->where(function($query) use ($gaji) {
                $query->where('status_pembayaran', 'belum_dibayar')
                      ->orWhere('updated_at', '>=', $gaji->created_at); // Jika diupdate setelah gaji dibuat, anggap belum dibayar
            })
            ->orderBy('tanggal', 'asc')
            ->get();
            
        // Ambil catatan gaji
        $catatanGaji = \App\Models\CatatanGaji::where('gaji_histori_id', $gaji->id)->get();

        $totalKasbon = $kasbonBelumDibayar->sum('nominal');

        $html = view('pegawai.detail-gaji-modal', compact('pegawai', 'gaji', 'kalkulasiGaji', 'totalKasbon', 'kasbonBelumDibayar', 'catatanGaji'))->render();

        return response()->json(['success' => true, 'html' => $html]);
    }

    private function calculateGaji($pegawaiId, $bulan, $tahun)
    {
        $pegawai = \App\Models\Pegawai::findOrFail($pegawaiId);

        // Cari tanggal gaji terakhir
        $lastGaji = GajiHistori::where('pegawai_id', $pegawaiId)
            ->orderBy('tanggal_gaji', 'desc')
            ->first();

        if ($lastGaji) {
            $startDate = $lastGaji->tanggal_gaji->copy()->addDay();
        } else {
            // Untuk pertama kali, mulai dari awal bulan
            $startDate = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        }
        $endDate = Carbon::create($tahun, $bulan)->endOfMonth();

        // Jika start date lebih besar dari end date, tidak ada data
        if ($startDate->gt($endDate)) {
            return [
                'gaji_dasar' => 0,
                'tambahan_1' => 0,
                'tambahan_2' => 0,
                'tambahan_3' => 0,
                'tambahan_4' => 0,
                'bonus_reguler' => 0,
                'total' => 0,
                'total_donat' => 0,
                'total_penjualan' => 0,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'number_of_days' => 0,
                'rincian' => [],
            ];
        }

        // Ambil rekap yang sudah divalidasi
        $validatedRekaps = \App\Models\Rekap::where('pegawai_id', $pegawaiId)
            ->where('status', 'validated')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();


        $numberOfDays = $validatedRekaps->count();
        $gajiDasar = 0;

        // Hitung gaji harian
        $gajiDasar = $pegawai->gaji_harian * $numberOfDays;

        // Hitung tambahan gaji berdasarkan target
        $tambahan1 = 0;
        $tambahan2 = 0;
        $tambahan3 = 0;
        $tambahan4 = 0;

        foreach ($validatedRekaps as $rekap) {
            $donat = $rekap->total_donat_terjual;
            if ($pegawai->target_1 > 0 && $donat >= $pegawai->target_1) {
                $tambahan1 += $pegawai->tambahan_gaji_1;
            }
            if ($pegawai->target_2 > 0 && $donat >= $pegawai->target_2) {
                $tambahan2 += $pegawai->tambahan_gaji_2;
            }
            if ($pegawai->target_3 > 0 && $donat >= $pegawai->target_3) {
                $tambahan3 += $pegawai->tambahan_gaji_3;
            }
            if ($pegawai->target_4 > 0 && $donat >= $pegawai->target_4) {
                $tambahan4 += $pegawai->tambahan_gaji_4;
            }
        }

        // Hitung bonus nominal per hari (kelipatan)
        $bonusReguler = 0;
        foreach ($validatedRekaps as $rekap) {
            $kelipatan = floor($rekap->total_donat_terjual / $pegawai->bonus_syarat);
            $bonusReguler += $kelipatan * $pegawai->bonus_nominal;
        }

        $totalDonat = $validatedRekaps->sum('total_donat_terjual');
        $totalPenjualan = $validatedRekaps->sum('total_uang_penjualan');

        $gajiTotal = $gajiDasar + $tambahan1 + $tambahan2 + $tambahan3 + $tambahan4 + $bonusReguler;

        // Buat rincian per hari
        $rincian = [];
        $no = 1;
        foreach ($validatedRekaps as $rekap) {
            $donat = $rekap->total_donat_terjual;
            $gajiHarian = $pegawai->gaji_harian;
            $tambahan = 0;
            if ($pegawai->target_1 > 0 && $donat >= $pegawai->target_1) {
                $tambahan += $pegawai->tambahan_gaji_1;
            }
            if ($pegawai->target_2 > 0 && $donat >= $pegawai->target_2) {
                $tambahan += $pegawai->tambahan_gaji_2;
            }
            if ($pegawai->target_3 > 0 && $donat >= $pegawai->target_3) {
                $tambahan += $pegawai->tambahan_gaji_3;
            }
            if ($pegawai->target_4 > 0 && $donat >= $pegawai->target_4) {
                $tambahan += $pegawai->tambahan_gaji_4;
            }
            $bonus = floor($donat / $pegawai->bonus_syarat) * $pegawai->bonus_nominal;

            $rincian[] = [
                'no' => $no++,
                'hari' => $rekap->tanggal->format('l'), // Nama hari
                'tanggal' => $rekap->tanggal->format('d/m/Y'),
                'gaji' => $gajiHarian + $tambahan,
                'bonus' => $bonus,
                'total_donat' => $donat,
            ];
        }

        return [
            'gaji_dasar' => $gajiDasar,
            'tambahan_1' => $tambahan1,
            'tambahan_2' => $tambahan2,
            'tambahan_3' => $tambahan3,
            'tambahan_4' => $tambahan4,
            'bonus_reguler' => $bonusReguler,
            'total' => $gajiTotal,
            'total_donat' => $totalDonat,
            'total_penjualan' => $totalPenjualan,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'number_of_days' => $numberOfDays,
            'rincian' => $rincian,
            'gaji_bersih' => $gajiTotal - ($totalKasbon ?? 0), // Tambahkan gaji bersih
            'gaji_harian_total' => $gajiDasar, // Tambahkan gaji dasar total untuk view
        ];
    }

    private function calculateGajiFlexible($pegawaiId, $tanggalMulai = null, $tanggalAkhir = null)
    {
        $pegawai = \App\Models\Pegawai::findOrFail($pegawaiId);

        // Jika tanggalMulai tidak diberikan, cari tanggal gaji terakhir
        if (!$tanggalMulai) {
            $lastGaji = GajiHistori::where('pegawai_id', $pegawaiId)
                ->orderBy('tanggal_gaji', 'desc')
                ->first();

            if ($lastGaji) {
                $tanggalMulai = $lastGaji->tanggal_gaji->copy()->addDay();
            } else {
                // Untuk pertama kali, mulai dari awal bulan ini
                $tanggalMulai = Carbon::now()->startOfMonth();
            }
        }

        // Jika tanggalAkhir tidak diberikan, gunakan hari ini
        if (!$tanggalAkhir) {
            $tanggalAkhir = Carbon::now();
        }

        // Pastikan tanggalMulai adalah Carbon instance
        if (!$tanggalMulai instanceof Carbon) {
            $tanggalMulai = Carbon::parse($tanggalMulai);
        }
        if (!$tanggalAkhir instanceof Carbon) {
            $tanggalAkhir = Carbon::parse($tanggalAkhir);
        }

        // Jika start date lebih besar dari end date, tidak ada data
        if ($tanggalMulai->gt($tanggalAkhir)) {
            return [
                'gaji_dasar' => 0,
                'tambahan_1' => 0,
                'tambahan_2' => 0,
                'tambahan_3' => 0,
                'tambahan_4' => 0,
                'bonus_reguler' => 0,
                'total' => 0,
                'total_donat' => 0,
                'total_penjualan' => 0,
                'start_date' => $tanggalMulai,
                'end_date' => $tanggalAkhir,
                'number_of_days' => 0,
                'rincian' => [],
            ];
        }

        // Ambil rekap yang sudah divalidasi dalam periode yang ditentukan
        $validatedRekaps = \App\Models\Rekap::where('pegawai_id', $pegawaiId)
            ->where('status', 'validated')
            ->whereBetween('tanggal', [$tanggalMulai, $tanggalAkhir])
            ->orderBy('tanggal')
            ->get();

        $numberOfDays = $validatedRekaps->count();
        $gajiDasar = 0;

        // Hitung gaji harian
        $gajiDasar = $pegawai->gaji_harian * $numberOfDays;

        // Hitung tambahan gaji berdasarkan target
        $tambahan1 = 0;
        $tambahan2 = 0;
        $tambahan3 = 0;
        $tambahan4 = 0;

        foreach ($validatedRekaps as $rekap) {
            $donat = $rekap->total_donat_terjual;
            if ($pegawai->target_1 > 0 && $donat >= $pegawai->target_1) {
                $tambahan1 += $pegawai->tambahan_gaji_1;
            }
            if ($pegawai->target_2 > 0 && $donat >= $pegawai->target_2) {
                $tambahan2 += $pegawai->tambahan_gaji_2;
            }
            if ($pegawai->target_3 > 0 && $donat >= $pegawai->target_3) {
                $tambahan3 += $pegawai->tambahan_gaji_3;
            }
            if ($pegawai->target_4 > 0 && $donat >= $pegawai->target_4) {
                $tambahan4 += $pegawai->tambahan_gaji_4;
            }
        }

        // Hitung bonus nominal per hari (kelipatan)
        $bonusReguler = 0;
        foreach ($validatedRekaps as $rekap) {
            $kelipatan = floor($rekap->total_donat_terjual / $pegawai->bonus_syarat);
            $bonusReguler += $kelipatan * $pegawai->bonus_nominal;
        }

        $totalDonat = $validatedRekaps->sum('total_donat_terjual');
        $totalPenjualan = $validatedRekaps->sum('total_uang_penjualan');

        $gajiTotal = $gajiDasar + $tambahan1 + $tambahan2 + $tambahan3 + $tambahan4 + $bonusReguler;

        // Buat rincian per hari
        $rincian = [];
        $no = 1;
        foreach ($validatedRekaps as $rekap) {
            $donat = $rekap->total_donat_terjual;
            $gajiHarian = $pegawai->gaji_harian;
            $tambahan = 0;
            if ($pegawai->target_1 > 0 && $donat >= $pegawai->target_1) {
                $tambahan += $pegawai->tambahan_gaji_1;
            }
            if ($pegawai->target_2 > 0 && $donat >= $pegawai->target_2) {
                $tambahan += $pegawai->tambahan_gaji_2;
            }
            if ($pegawai->target_3 > 0 && $donat >= $pegawai->target_3) {
                $tambahan += $pegawai->tambahan_gaji_3;
            }
            if ($pegawai->target_4 > 0 && $donat >= $pegawai->target_4) {
                $tambahan += $pegawai->tambahan_gaji_4;
            }
            $bonus = floor($donat / $pegawai->bonus_syarat) * $pegawai->bonus_nominal;

            $rincian[] = [
                'no' => $no++,
                'hari' => $rekap->tanggal->format('l'), // Nama hari
                'tanggal' => $rekap->tanggal->format('d/m/Y'),
                'gaji' => $gajiHarian + $tambahan,
                'bonus' => $bonus,
                'total_donat' => $donat,
            ];
        }

        return [
            'gaji_dasar' => $gajiDasar,
            'tambahan_1' => $tambahan1,
            'tambahan_2' => $tambahan2,
            'tambahan_3' => $tambahan3,
            'tambahan_4' => $tambahan4,
            'bonus_reguler' => $bonusReguler,
            'total' => $gajiTotal,
            'total_donat' => $totalDonat,
            'total_penjualan' => $totalPenjualan,
            'start_date' => $tanggalMulai,
            'end_date' => $tanggalAkhir,
            'number_of_days' => $numberOfDays,
            'rincian' => $rincian,
        ];
    }
}
