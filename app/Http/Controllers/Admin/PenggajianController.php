<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\GajiHistori;
use App\Models\Rekap;
use App\Models\Kasbon;
use App\Models\CatatanGaji;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PenggajianController extends Controller
{
    public function index()
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            Session::flash('error', 'Akses ditolak. Hanya admin yang bisa mengakses.');
            return redirect()->route('login');
        }

        // Ambil hanya pegawai yang bukan admin
        $pegawai = Pegawai::with('outlet')->where('role', '!=', 'admin')->get();
        return view('admin.penggajian', compact('pegawai'));
    }

    public function show($pegawaiId)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $pegawai = Pegawai::with('outlet')->findOrFail($pegawaiId);
        $gajiHistori = GajiHistori::where('pegawai_id', $pegawaiId)->orderBy('tanggal_gaji', 'desc')->paginate(10);

        // Cek apakah ada periode yang sudah disimpan di session
        $periodeSession = Session::get('periode_gaji_' . $pegawaiId);
        
        if ($periodeSession && isset($periodeSession['tanggal_mulai']) && isset($periodeSession['tanggal_akhir'])) {
            // Gunakan periode yang sudah disimpan di session
            $tanggalMulai = Carbon::parse($periodeSession['tanggal_mulai']);
            $tanggalAkhir = Carbon::parse($periodeSession['tanggal_akhir']);
            $kalkulasiGaji = $this->calculateGajiFlexible($pegawaiId, $tanggalMulai, $tanggalAkhir);
        } else {
            // Hitung gaji untuk periode saat ini (dari gaji terakhir hingga sekarang)
            $now = Carbon::now();
            $kalkulasiGaji = $this->calculateGajiFlexible($pegawaiId, null, $now);
            // Simpan periode awal ke session
            Session::put('periode_gaji_' . $pegawaiId, [
                'tanggal_mulai' => $kalkulasiGaji['start_date']->format('Y-m-d'),
                'tanggal_akhir' => $kalkulasiGaji['end_date']->format('Y-m-d')
            ]);
        }
        
        $startDate = $kalkulasiGaji['start_date'];

        // Ambil kasbon yang belum dibayar (status approved dan status_pembayaran belum_dibayar)
        $kasbonBelumDibayar = Kasbon::where('pegawai_id', $pegawaiId)
            ->where('status', 'approved')
            ->where('status_pembayaran', 'belum_dibayar')
            ->orderBy('tanggal', 'asc')
            ->get();

        $totalKasbon = $kasbonBelumDibayar->sum('nominal');

        // Ambil catatan gaji dari session (untuk catatan sementara sebelum divalidasi)
        $tempCatatanGaji = Session::get('catatan_gaji_' . $pegawaiId, []);

        return view('admin.penggajian-detail', compact('pegawai', 'gajiHistori', 'kalkulasiGaji', 'startDate', 'kasbonBelumDibayar', 'totalKasbon', 'tempCatatanGaji'));
    }

    public function detailGaji($pegawaiId, $gajiId)
    {
        if (!Session::has('user') || Session::get('user')->role !== 'admin') {
            return response()->json(['error' => 'Akses ditolak'], 403);
        }

        $pegawai = Pegawai::with('outlet')->findOrFail($pegawaiId);
        $gaji = GajiHistori::where('pegawai_id', $pegawaiId)->where('id', $gajiId)->firstOrFail();

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

        $totalKasbon = $kasbonBelumDibayar->sum('nominal');

        // Ambil catatan gaji dari database (yang sudah divalidasi)
        $catatanGaji = CatatanGaji::where('gaji_histori_id', $gajiId)
            ->orderBy('created_at', 'asc')
            ->get();

        $html = view('admin.penggajian-detail-gaji', compact('pegawai', 'gaji', 'kalkulasiGaji', 'kasbonBelumDibayar', 'totalKasbon', 'catatanGaji'))->render();

        // Remove navbar and other layout elements from the rendered HTML if present
        // Assuming the detail-gaji blade is a partial without layout, so no change needed here
        // If the blade extends a layout, we need to create a partial view without navbar for modal

        return response()->json(['html' => $html]);
    }

    public function calculateGajiAjax(Request $request, $pegawaiId)
    {
        try {
            \Log::info('calculateGajiAjax called', [
                'pegawai_id' => $pegawaiId,
                'tanggal_mulai' => $request->get('tanggal_mulai'),
                'tanggal_akhir' => $request->get('tanggal_akhir'),
                'all_data' => $request->all()
            ]);

            $tanggalMulai = $request->get('tanggal_mulai');
            $tanggalAkhir = $request->get('tanggal_akhir');
            
            if ($tanggalMulai && $tanggalAkhir) {
                $mulai = Carbon::parse($tanggalMulai);
                $akhir = Carbon::parse($tanggalAkhir);
                $kalkulasi = $this->calculateGajiFlexible($pegawaiId, $mulai, $akhir);
                
                // Simpan periode ke session agar tetap sama setelah refresh
                Session::put('periode_gaji_' . $pegawaiId, [
                    'tanggal_mulai' => $tanggalMulai,
                    'tanggal_akhir' => $tanggalAkhir
                ]);
            } else {
                // Fallback ke bulan/tahun jika tidak ada tanggal
                $bulan = $request->get('bulan', Carbon::now()->month);
                $tahun = $request->get('tahun', Carbon::now()->year);
                $kalkulasi = $this->calculateGaji($pegawaiId, $bulan, $tahun);
            }
            
            // Hitung total catatan gaji dari session
            $tempCatatanGaji = Session::get('catatan_gaji_' . $pegawaiId, []);
            $totalTambahanGaji = 0;
            $totalPenguranganGaji = 0;
            foreach ($tempCatatanGaji as $catatan) {
                if ($catatan['jenis'] === 'tambahan') {
                    $totalTambahanGaji += $catatan['jumlah'];
                } else {
                    $totalPenguranganGaji += $catatan['jumlah'];
                }
            }
            $kalkulasi['total_catatan_tambahan'] = $totalTambahanGaji;
            $kalkulasi['total_catatan_pengurangan'] = $totalPenguranganGaji;
            $kalkulasi['catatan_gaji'] = $tempCatatanGaji;
            
            \Log::info('calculateGajiAjax success', [
                'gaji_dasar' => $kalkulasi['gaji_dasar'],
                'total' => $kalkulasi['total'],
                'number_of_days' => $kalkulasi['number_of_days']
            ]);
            return response()->json($kalkulasi);
        } catch (\Exception $e) {
            \Log::error('Error calculating gaji: ' . $e->getMessage(), [
                'pegawai_id' => $pegawaiId,
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Terjadi kesalahan saat menghitung gaji: ' . $e->getMessage()], 500);
        }
    }

    public function validateGaji(Request $request, $pegawaiId)
    {
        // Validasi input dasar
        $request->validate([
            'tanggal_mulai' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_mulai',
            'periode_keterangan' => 'nullable|string|max:255',
            'gaji_total' => 'required|integer|min:0',
        ]);

        $tanggalMulai = Carbon::parse($request->tanggal_mulai);
        $tanggalAkhir = Carbon::parse($request->tanggal_akhir);

        // Cek apakah sudah ada gaji untuk periode yang overlap
        $existing = GajiHistori::where('pegawai_id', $pegawaiId)
            ->where(function($query) use ($tanggalMulai, $tanggalAkhir) {
                $query->whereBetween('periode_mulai', [$tanggalMulai, $tanggalAkhir])
                      ->orWhereBetween('periode_akhir', [$tanggalMulai, $tanggalAkhir])
                      ->orWhere(function($q) use ($tanggalMulai, $tanggalAkhir) {
                          $q->where('periode_mulai', '<=', $tanggalMulai)
                            ->where('periode_akhir', '>=', $tanggalAkhir);
                      });
            })
            ->first();

        if ($existing) {
            return response()->json(['error' => 'Sudah ada gaji untuk periode yang overlap dengan periode ini.'], 400);
        }

        // Hitung gaji harian berdasarkan rekap yang divalidasi
        $kalkulasiGaji = $this->calculateGajiFlexible($pegawaiId, $tanggalMulai, $tanggalAkhir);
        $gajiHarian = $kalkulasiGaji['total'];

        // Hitung total kasbon yang belum dibayar (status pembayaran belum lunas)
        $totalKasbon = Kasbon::where('pegawai_id', $pegawaiId)
            ->where('status', 'approved')
            ->where('status_pembayaran', 'belum_dibayar')
            ->sum('nominal');

        // Hitung total catatan gaji dari session
        $tempCatatanGaji = Session::get('catatan_gaji_' . $pegawaiId, []);
        $totalTambahanGaji = 0;
        $totalPenguranganGaji = 0;
        foreach ($tempCatatanGaji as $catatan) {
            if ($catatan['jenis'] === 'tambahan') {
                $totalTambahanGaji += $catatan['jumlah'];
            } else {
                $totalPenguranganGaji += $catatan['jumlah'];
            }
        }

        // Hitung gaji bersih (gaji harian + tambahan catatan - pengurangan catatan - kasbon)
        $gajiBersih = $gajiHarian + $totalTambahanGaji - $totalPenguranganGaji - $totalKasbon;

        // Cek kesesuaian gaji total yang dikirim dengan perhitungan
        if ($request->gaji_total != $gajiBersih) {
            return response()->json(['error' => 'Gaji total tidak sesuai dengan perhitungan.'], 400);
        }

        // Cek jika gaji bersih <= 0, tolak validasi
        if ($gajiBersih <= 0) {
            return response()->json(['error' => 'Gaji bersih tidak boleh 0 atau kurang. Validasi dibatalkan.'], 400);
        }

        // Simpan data gaji ke database
        $gajiHistori = new GajiHistori();
        $gajiHistori->pegawai_id = $pegawaiId;
        $gajiHistori->tanggal_gaji = $tanggalAkhir; // Tanggal gaji = tanggal akhir periode
        $gajiHistori->periode_mulai = $tanggalMulai;
        $gajiHistori->periode_akhir = $tanggalAkhir;
        $gajiHistori->periode_keterangan = $request->periode_keterangan;
        $gajiHistori->gaji_harian = $gajiHarian;
        $gajiHistori->kasbon = $totalKasbon;
        $gajiHistori->gaji_bersih = $gajiBersih;
        $gajiHistori->gaji_total = $gajiBersih; // untuk backward compatibility
        $gajiHistori->status = 'paid';
        $gajiHistori->rincian_gaji_harian = $kalkulasiGaji['rincian']; // Simpan rincian gaji harian
        
        // Simpan GajiHistori terlebih dahulu untuk mendapatkan ID
        $gajiHistori->save();

        // Ambil catatan gaji dari session SEBELUM session dihapus
        $tempCatatanGaji = Session::get('catatan_gaji_' . $pegawaiId, []);
        
        // Simpan catatan gaji ke database
        foreach ($tempCatatanGaji as $catatan) {
            CatatanGaji::create([
                'gaji_histori_id' => $gajiHistori->id,
                'pegawai_id' => $pegawaiId,
                'jenis' => $catatan['jenis'],
                'jumlah' => $catatan['jumlah'],
                'catatan' => $catatan['catatan'] ?? '',
            ]);
        }
        
        // Hapus session periode dan catatan gaji setelah semua data disimpan
        Session::forget('periode_gaji_' . $pegawaiId);
        Session::forget('catatan_gaji_' . $pegawaiId);

        // Update status kasbon menjadi lunas hanya yang belum dibayar
        $kasbonBelumDibayar = Kasbon::where('pegawai_id', $pegawaiId)
            ->where('status', 'approved')
            ->where('status_pembayaran', 'belum_dibayar')
            ->get();

        foreach ($kasbonBelumDibayar as $kasbon) {
            $kasbon->status_pembayaran = 'lunas';
            $kasbon->save();
        }

        Session::flash('success', 'Gaji berhasil divalidasi dan status kasbon diperbarui.');
        return response()->json(['message' => 'Gaji berhasil divalidasi dan status kasbon diperbarui.']);
    }

    private function calculateGajiFlexible($pegawaiId, $tanggalMulai = null, $tanggalAkhir = null)
    {
        try {
            $pegawai = Pegawai::findOrFail($pegawaiId);

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
            $validatedRekaps = Rekap::where('pegawai_id', $pegawaiId)
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
                if ($pegawai->bonus_syarat > 0) {
                    $kelipatan = floor($rekap->total_donat_terjual / $pegawai->bonus_syarat);
                    $bonusReguler += $kelipatan * $pegawai->bonus_nominal;
                }
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
                $bonus = 0;
                if ($pegawai->bonus_syarat > 0) {
                    $bonus = floor($donat / $pegawai->bonus_syarat) * $pegawai->bonus_nominal;
                }

                $rincian[] = [
                    'no' => $no++,
                    'hari' => $this->getDayNameIndonesian($rekap->tanggal->dayOfWeek), // Nama hari dalam bahasa Indonesia
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
        } catch (\Exception $e) {
            \Log::error('Error in calculateGajiFlexible: ' . $e->getMessage());
            throw $e;
        }
    }

    private function calculateGaji($pegawaiId, $bulan, $tahun)
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);

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
        $validatedRekaps = Rekap::where('pegawai_id', $pegawaiId)
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
                'hari' => $this->getDayNameIndonesian($rekap->tanggal->dayOfWeek), // Nama hari dalam bahasa Indonesia
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
        ];
    }

    private function getDayNameIndonesian($dayOfWeek)
    {
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu'
        ];

        return $days[$dayOfWeek] ?? 'Tidak Diketahui';
    }

    public function tambahCatatanGaji(Request $request, $pegawaiId)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate([
                'jenis' => 'required|in:tambahan,pengurangan',
                'jumlah' => 'required|integer|min:0',
                'catatan' => 'nullable|string|max:255',
            ]);

            $catatan = [
                'jenis' => $request->jenis,
                'jumlah' => $request->jumlah,
                'catatan' => $request->catatan ?? '',
            ];

            $tempCatatanGaji = Session::get('catatan_gaji_' . $pegawaiId, []);
            $tempCatatanGaji[] = $catatan;
            Session::put('catatan_gaji_' . $pegawaiId, $tempCatatanGaji);

            return response()->json(['success' => 'Catatan gaji berhasil ditambahkan']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menambahkan catatan gaji'], 500);
        }
    }

    public function hapusCatatanGaji(Request $request, $pegawaiId)
    {
        try {
            if (!Session::has('user') || Session::get('user')->role !== 'admin') {
                return response()->json(['error' => 'Akses ditolak'], 403);
            }

            $request->validate(['index' => 'required|integer']);
            $tempCatatanGaji = Session::get('catatan_gaji_' . $pegawaiId, []);
            
            if (isset($tempCatatanGaji[$request->index])) {
                unset($tempCatatanGaji[$request->index]);
                $tempCatatanGaji = array_values($tempCatatanGaji); // Reindex array
                Session::put('catatan_gaji_' . $pegawaiId, $tempCatatanGaji);
                return response()->json(['success' => 'Catatan gaji berhasil dihapus']);
            }
            
            return response()->json(['error' => 'Catatan tidak ditemukan'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal menghapus catatan gaji'], 500);
        }
    }
}
