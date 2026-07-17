<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Detail Gaji - {{ $pegawai->nama }}</h5>

                <!-- Informasi Periode -->
                @if($gaji->periode_mulai && $gaji->periode_akhir)
                    <div class="alert alert-info mb-3">
                        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($gaji->periode_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($gaji->periode_akhir)->format('d/m/Y') }}
                        @if($gaji->periode_keterangan)
                            <br><strong>Keterangan:</strong> {{ $gaji->periode_keterangan }}
                        @endif
                        <br><strong>Tanggal Gaji:</strong> {{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d F Y') }}
                    </div>
                @else
                    <div class="alert alert-info mb-3">
                        <strong>Tanggal Gaji:</strong> {{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d F Y') }}
                        <br><small class="text-muted">(Data lama - periode tidak tersimpan)</small>
                    </div>
                @endif

                <!-- Rincian Gaji Harian -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gaji-breakdown">
                            <h6>Rincian Gaji Harian</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Hari</th>
                                            <th>Tanggal</th>
                                            <th>Donat</th>
                                            <th>Gaji</th>
                                            <th>Bonus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalGaji = 0;
                                            $totalBonus = 0;
                                        @endphp
                                        @forelse($kalkulasiGaji['rincian'] as $rincian)
                                            @php
                                                $totalGaji += $rincian['gaji'];
                                                $totalBonus += $rincian['bonus'];
                                            @endphp
                                            <tr>
                                                <td>{{ $rincian['no'] }}</td>
                                                <td>{{ $rincian['hari'] }}</td>
                                                <td>{{ $rincian['tanggal'] }}</td>
                                                <td>{{ $rincian['total_donat'] }}</td>
                                                <td>Rp {{ number_format($rincian['gaji'], 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($rincian['bonus'], 0, ',', '.') }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada data operasional.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if(count($kalkulasiGaji['rincian']) > 0)
                                    <tfoot>
                                        <tr class="table-info">
                                            <th colspan="4">Total</th>
                                            <th>Rp {{ number_format($totalGaji, 0, ',', '.') }}</th>
                                            <th>Rp {{ number_format($totalBonus, 0, ',', '.') }}</th>
                                        </tr>
                                    </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Catatan Gaji -->
                @if(isset($catatanGaji) && $catatanGaji->count() > 0)
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gaji-breakdown">
                            <h6>Catatan Gaji</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($catatanGaji as $index => $catatan)
                                            <tr class="{{ $catatan->jenis === 'tambahan' ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                                <td>{{ $index + 1 }}</td>
                                                <td>
                                                    @if ($catatan->jenis === 'tambahan')
                                                        <span class="badge bg-success badge-sm">
                                                            Penambahan
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger badge-sm">
                                                           Pengurangan
                                                        </span>
                                                    @endif
                                                </td>
                                                <td class="fw-semibold {{ $catatan->jenis === 'tambahan' ? 'text-success' : 'text-danger' }}">
                                                    Rp {{ number_format($catatan->jumlah, 0, ',', '.') }}
                                                </td>
                                                <td>{{ $catatan->catatan ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Kasbon Pegawai yang Belum Dibayar -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gaji-breakdown">
                            <h6>Kasbon Pegawai yang Belum Dibayar</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Tanggal</th>
                                            <th>Nominal</th>
                                            <th>Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($kasbonBelumDibayar as $index => $kasbon)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ \Carbon\Carbon::parse($kasbon->tanggal)->format('d/m/Y') }}</td>
                                                <td>Rp {{ number_format($kasbon->nominal, 0, ',', '.') }}</td>
                                                <td>{{ $kasbon->keterangan }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak ada kasbon yang belum dibayar.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2">Total Kasbon</th>
                                            <th>Rp {{ number_format($totalKasbon, 0, ',', '.') }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Rekap Gaji -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="gaji-breakdown">
                            <h6>Rekap Gaji</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped rekap-gaji-table">
                                    <thead>
                                        <tr>
                                            <th>Keterangan</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Gaji Harian</td>
                                            <td>Rp {{ number_format($gaji->gaji_harian, 0, ',', '.') }}</td>
                                        </tr>
                                        @php
                                            $totalTambahanGajiRekap = isset($catatanGaji) ? $catatanGaji->where('jenis', 'tambahan')->sum('jumlah') : 0;
                                            $totalPenguranganGajiRekap = isset($catatanGaji) ? $catatanGaji->where('jenis', 'pengurangan')->sum('jumlah') : 0;
                                        @endphp
                                        @if($totalTambahanGajiRekap > 0)
                                        <tr>
                                            <td class="text-success">Tambahan Catatan</td>
                                            <td class="text-success">Rp {{ number_format($totalTambahanGajiRekap, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                        @if($totalPenguranganGajiRekap > 0)
                                        <tr>
                                            <td class="text-danger">Pengurangan Catatan</td>
                                            <td class="text-danger">Rp {{ number_format($totalPenguranganGajiRekap, 0, ',', '.') }}</td>
                                        </tr>
                                        @endif
                                        <tr>
                                            <td>Kasbon</td>
                                            <td>Rp {{ number_format($gaji->kasbon, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr class="table-info">
                                            <td><strong>Gaji Bersih</strong></td>
                                            <td><strong>Rp {{ number_format($gaji->gaji_bersih, 0, ',', '.') }}</strong></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Gaji Bersih Total -->
                <div class="row">
                    <div class="col-md-4 offset-md-8">
                        <div class="gaji-total">
                            <h4>Gaji Bersih</h4>
                            <h2>Rp {{ number_format(($gaji->gaji_bersih ?? ($kalkulasiGaji['total'] - $totalKasbon)), 0, ',', '.') }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .gaji-breakdown {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    .gaji-total {
        background-color: #e3f2fd;
        border: 2px solid #2196f3;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    .badge-sm {
        font-size: 0.75em; /* Smaller font size for badges */
        padding: 0.2em 0.4em; /* Adjust padding */
    }
    .bg-success-subtle {
        background-color: #d4edda !important; /* Bootstrap's success background color */
    }
    .bg-danger-subtle {
        background-color: #f8d7da !important; /* Bootstrap's danger background color */
    }
    @media (max-width: 767.98px) {
        .table th:nth-child(1),
        .table td:nth-child(1) {
            width: 50px; /* Adjust as needed for 'No' column */
            min-width: 50px;
            max-width: 50px;
            word-wrap: break-word;
        }
    }
    
    /* Add a specific class to the Rekap Gaji table */
    .rekap-gaji-table th,
    .rekap-gaji-table td {
        padding: 0.75rem !important;
    }
    
    @media (max-width: 767.98px) {
        .rekap-gaji-table {
            table-layout: fixed !important;
        }
        
        .rekap-gaji-table th:first-child,
        .rekap-gaji-table td:first-child {
            width: 60% !important;
        }
        
        .rekap-gaji-table th:last-child,
        .rekap-gaji-table td:last-child {
            width: 40% !important;
        }
    }
</style>