@extends('layouts.app')

@section('title', 'Detail Rekap Harian - {{ $outlet->nama }}')

@section('css')
<style>
    .card-body p {
        margin-bottom: 10px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .table th, .table td {
        vertical-align: middle;
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .btn-sm {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }
        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-file-text me-2"></i>Detail Rekap Harian - {{ $outlet->nama }}</h5>
                <p>Alamat: {{ $outlet->alamat }}</p>
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Rekap Hari Ini -->
                @if ($rekapHariIni)
                    <h6>Rekap Hari Ini ({{ \Carbon\Carbon::today()->format('d-m-Y') }})</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Jumlah Kloter</th>
                                    <th>Total Donat Tersedia</th>
                                    <th>Total Donat Terjual</th>
                                    <th>Pendapatan</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($rekapHariIni->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $kloters[$rekapHariIni->id]->count() }}</td>
                                    <td>{{ $rekapHariIni->operasional->total_donat_harian }}</td>
                                    <td>{{ $rekapHariIni->total_donat_terjual }}</td>
                                    <td>
                                        @php
                                            $totalPendapatan = $rekapHariIni->total_uang;
                                            foreach ($rekapHariIni->catatanOperasionals as $catatan) {
                                                if ($catatan->jenis === 'pemasukan' && !$catatan->kategori_kemasan) {
                                                    $totalPendapatan += $catatan->jumlah;
                                                } elseif ($catatan->jenis === 'pengeluaran' && !$catatan->kategori_kemasan) {
                                                    $totalPendapatan -= $catatan->jumlah;
                                                }
                                            }
                                        @endphp
                                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                    </td>
                                    <td><span class="badge bg-{{ $rekapHariIni->status === 'pending' ? 'warning' : 'success' }}">{{ ucfirst($rekapHariIni->status) }}</span></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $rekapHariIni->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted">Rekap hari ini belum dibuat oleh pegawai.</p>
                @endif

                <!-- Histori Rekap Harian -->
                <h6>Histori Rekap Harian</h6>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Jumlah Kloter</th>
                                <th>Total Donat Tersedia</th>
                                <th>Total Donat Terjual</th>
                                <th>Pendapatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($rekaps as $index => $rekap)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($rekap->tanggal)->format('d-m-Y') }}</td>
                                    <td>{{ $kloters[$rekap->id]->count() }}</td>
                                    <td>{{ $rekap->operasional->total_donat_harian }}</td>
                                    <td>{{ $rekap->total_donat_terjual }}</td>
                                    <td>
                                        @php
                                            $totalPendapatan = $rekap->total_uang;
                                            foreach ($rekap->catatanOperasionals as $catatan) {
                                                if ($catatan->jenis === 'pemasukan' && !$catatan->kategori_kemasan) {
                                                    $totalPendapatan += $catatan->jumlah;
                                                } elseif ($catatan->jenis === 'pengeluaran' && !$catatan->kategori_kemasan) {
                                                    $totalPendapatan -= $catatan->jumlah;
                                                }
                                            }
                                        @endphp
                                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $rekap->id }}">
                                            <i class="bi bi-eye"></i> Detail
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada rekap harian yang divalidasi.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <a href="{{ route('admin.rekap.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Outlet
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detail Rekap -->
@foreach ($rekaps->merge($rekapHariIni ? collect([$rekapHariIni]) : collect([])) as $rekap)
<div class="modal fade" id="detailModal{{ $rekap->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Rekap Harian - {{ $outlet->nama }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tanggal:</strong> {{ \Carbon\Carbon::parse($rekap->tanggal)->format('d-m-Y') }}</p>
                <p><strong>Jumlah Kloter:</strong> {{ $kloters[$rekap->id]->count() }}</p>
                <p><strong>Total Donat Tersedia:</strong> {{ $rekap->operasional->total_donat_harian }}</p>
                <p><strong>Total Donat Terjual:</strong> {{ $rekap->total_donat_terjual }}</p>
                <p><strong>Stok Kemasan:</strong></p>
                <ul>
                    @php
                        // Hitung sisa stok keseluruhan
                        $sisaMika = $rekap->sisa_mika;
                        $sisaDus1 = $rekap->sisa_dus1;
                        $sisaDus2 = $rekap->sisa_dus2;
                        $sisaDus3 = $rekap->sisa_dus3;
                        $sisaBox = $rekap->sisa_box;

                        // Penyesuaian dari catatan operasional
                        foreach ($rekap->catatanOperasionals as $catatan) {
                            if ($catatan->kategori_kemasan) {
                                if ($catatan->kategori_kemasan === 'mika') {
                                    $sisaMika += $catatan->jenis === 'pemasukan' ? $catatan->jumlah : -$catatan->jumlah;
                                } elseif ($catatan->kategori_kemasan === 'dus1') {
                                    $sisaDus1 += $catatan->jenis === 'pemasukan' ? $catatan->jumlah : -$catatan->jumlah;
                                } elseif ($catatan->kategori_kemasan === 'dus2') {
                                    $sisaDus2 += $catatan->jenis === 'pemasukan' ? $catatan->jumlah : -$catatan->jumlah;
                                } elseif ($catatan->kategori_kemasan === 'dus3') {
                                    $sisaDus3 += $catatan->jenis === 'pemasukan' ? $catatan->jumlah : -$catatan->jumlah;
                                } elseif ($catatan->kategori_kemasan === 'box') {
                                    $sisaBox += $catatan->jenis === 'pemasukan' ? $catatan->jumlah : -$catatan->jumlah;
                                }
                            }
                        }
                    @endphp
                    <li>Mika: {{ $sisaMika }}</li>
                    <li>Dus 1: {{ $sisaDus1 }}</li>
                    <li>Dus 2: {{ $sisaDus2 }}</li>
                    <li>Dus 3: {{ $sisaDus3 }}</li>
                    <li>Box: {{ $sisaBox }}</li>
                </ul>
                <p><strong>Total Uang Penjualan:</strong> Rp {{ number_format($rekap->total_uang, 0, ',', '.') }}</p>
                <h6>Rekap Uang</h6>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris Pemasukan -->
                            <tr>
                                <td>1</td>
                                <td>Pemasukan</td>
                                <td>Rp {{ number_format($rekap->total_uang, 0, ',', '.') }}</td>
                                <td>Penjualan</td>
                            </tr>
                            <!-- Baris Catatan Operasional -->
                            @forelse ($rekap->catatanOperasionals as $index => $catatan)
                                <tr>
                                    <td>{{ $index + 2 }}</td>
                                    <td>{{ ucfirst($catatan->jenis) }}</td>
                                    <td>Rp {{ number_format($catatan->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ $catatan->catatan ?? '-' }} {{ $catatan->kategori_kemasan ? "(Kemasan: " . ucfirst($catatan->kategori_kemasan) . ")" : '' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada catatan operasional.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Total Pendapatan -->
                @php
                    $totalPendapatan = $rekap->total_uang;
                    foreach ($rekap->catatanOperasionals as $catatan) {
                        if ($catatan->jenis === 'pemasukan' && !$catatan->kategori_kemasan) {
                            $totalPendapatan += $catatan->jumlah;
                        } elseif ($catatan->jenis === 'pengeluaran' && !$catatan->kategori_kemasan) {
                            $totalPendapatan -= $catatan->jumlah;
                        }
                    }
                @endphp
                <p><strong>Total Pendapatan (Pemasukan ± Catatan Operasional):</strong> Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@section('js')
<script>
    function validasiRekap(rekapId) {
        if (confirm('Validasi rekap ini dan tutup operasional?')) {
            fetch('{{ route('admin.rekap.validasi', [$outlet->id, ':rekap_id']) }}'.replace(':rekap_id', rekapId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error || 'Gagal memvalidasi rekap') });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Gagal memvalidasi rekap'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memvalidasi rekap: ' + error.message);
            });
        }
    }
</script>
@endsection