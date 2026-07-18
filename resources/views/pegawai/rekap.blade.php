@extends('layouts.app')

@section('title', 'Rekap Harian - {{ $outlet->nama }}')

@section('css')
    <style>
        .rekap-card {
            background: white;
            border-radius: var(--border-radius);
            border: 1px solid var(--border);
            margin-bottom: 1.15rem;
        }

        .section-title {
            font-size: 0.72rem;
            font-weight: 650;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 0.7rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title i {
            color: var(--brand);
            font-size: 1rem;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 0.75rem;
            margin-bottom: 1.25rem;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 0.9rem 1rem;
            border: 1px solid var(--border);
        }

        .summary-value {
            font-size: 1.15rem;
            font-weight: 650;
            color: var(--dark-color);
            margin-bottom: 0.25rem;
        }

        .summary-value.primary {
            color: var(--brand);
        }

        .summary-value.info {
            color: var(--info-color);
        }

        .summary-label {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 0.5rem;
            margin-bottom: 1.15rem;
        }

        .info-item {
            text-align: center;
            padding: 0.75rem;
            background: #f8f9fc;
            border-radius: var(--border-radius-sm);
            border: 1px solid var(--border);
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 650;
            color: var(--dark-color);
            margin-bottom: 0.2rem;
        }

        .info-label {
            font-size: 0.72rem;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            font-size: 0.8rem;
            border-radius: 6px;
        }

        .payment-method-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .total-highlight {
            background: #eef6ff;
            color: #1e40af;
            border: 1px solid #dbeafe;
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            text-align: center;
            margin: 1rem 0;
        }

        .total-highlight--muted {
            background: #f1f5f9;
            color: #334155;
            border: 1px solid var(--border);
        }

        .total-amount {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .total-label {
            font-size: 0.9rem;
            opacity: 0.85;
        }

        .save-section {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            text-align: center;
            margin-top: 1.35rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--secondary-color);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }

            .info-grid {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.5rem;
            }

            .rekap-card .card-body {
                padding: 1rem;
            }

            .save-section {
                padding: 1.25rem;
            }
        }

        @media (max-width: 576px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .table-responsive {
                font-size: 0.8rem;
            }

            .btn-action {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
            }
        }
    </style>
@endsection

@section('back-button')
    <a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    <div class="ui-page ui-page--wide">
        <header class="ui-header">
            <div>
                <h1>Rekap Harian</h1>
                <p>
                    {{ $outlet->nama }} · {{ $outlet->alamat }}
                    @if(isset($tanggal))
                        · {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
                    @endif
                </p>
            </div>
        </header>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show mb-3" role="alert">
                <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Summary Cards -->
        <div class="summary-grid">
            @php
                $totalTunai = 0;
                if ($operasional) {
                    foreach ($operasional->transaksis as $transaksi) {
                        if (strtolower($transaksi->metode_pembayaran) === 'tunai') {
                            $totalTunai += $transaksi->total_harga;
                        }
                    }
                }

                $netCatatan = 0;
                foreach ($tempCatatan as $catatan) {
                    if (($catatan['jenis'] ?? '') === 'pemasukan') {
                        $netCatatan += (int) ($catatan['jumlah'] ?? 0);
                    } elseif (($catatan['jenis'] ?? '') === 'pengeluaran') {
                        $netCatatan -= (int) ($catatan['jumlah'] ?? 0);
                    }
                }
                $cashDiPegawai = $totalTunai + $netCatatan;
            @endphp
            <div class="summary-card">
                <div class="summary-value">Rp {{ number_format($totalUangPenjualan, 0, ',', '.') }}</div>
                <div class="summary-label">Total Uang Penjualan</div>
            </div>
            <div class="summary-card bg-success">
                <div class="summary-value" style="color: white;">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
                <div class="summary-label" style="color: white;">Total Pendapatan Hari Ini</div>
            </div>
            <div class="summary-card bg-secondary">
                <div class="summary-value" style="color: white;">Rp {{ number_format($cashDiPegawai, 0, ',', '.') }}</div>
                <div class="summary-label" style="color: white;">Cash Di Pegawai</div>
            </div>
        </div>

        <!-- Tabel Donat -->
        <div class="rekap-card">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="bi bi-pie-chart"></i>
                    Ringkasan Donat
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Total Donat</th>
                                <th>Donat Terjual</th>
                                <th>Sisa Donat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="fw-semibold">{{ $totalDonat }}</td>
                                <td class="fw-semibold">{{ $totalDonatTerjual }}</td>
                                <td class="fw-semibold">{{ $totalDonat - $totalDonatTerjual }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Daftar Transaksi -->
        <div class="rekap-card">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="bi bi-receipt"></i>
                    Daftar Transaksi
                </h6>

                @if ($operasional && $operasional->transaksis->count() > 0)
                    <div class="table-responsive">
                        @php
                            // Sort transaksis by created_at descending (latest first)
                            $sortedTransaksis = $operasional->transaksis->sortByDesc('created_at')->values();

                            // Mapping kemasan to codes
                            $itemCodeMap = [
                                'mika' => 'M',
                                'dus1' => 'D1',
                                'dus2' => 'D2',
                                'dus3' => 'D3',
                                'box' => 'B',
                                'box12' => 'B12',
                                'lilin' => 'L',
                            ];
                        @endphp
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>Transaksi</th>
                                    <th>Item</th>
                                    <th>Metode Pembayaran</th>
                                    <th>Total</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sortedTransaksis as $index => $transaksi)
                                    <tr>
                                        <td class="fw-semibold">#{{ $transaksi->no_transaksi ?? ($index + 1) }}</td>
                                        <td>
                                            @php
                                                // Hitung jumlah masing-masing item
                                                $itemCounts = [];
                                                if (isset($transaksi->items) && count($transaksi->items) > 0) {
                                                    foreach ($transaksi->items as $item) {
                                                        $kemasan = $item->kemasan ?? '';
                                                        $code = $itemCodeMap[$kemasan] ?? null;
                                                        $jumlah = $item->jumlah ?? 1;
                                                        if ($code) {
                                                            if (!isset($itemCounts[$code])) {
                                                                $itemCounts[$code] = 0;
                                                            }
                                                            $itemCounts[$code] += $jumlah;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            @if(count($itemCounts) > 0)
                                                @foreach($itemCounts as $code => $jumlah)
                                                    <span class="badge bg-info">{{ $code }} ({{ $jumlah }})</span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary payment-method-badge">
                                                {{ ucfirst($transaksi->metode_pembayaran) }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-success">Rp
                                            {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                        <td>{{ $transaksi->created_at ? $transaksi->created_at->format('H:i:s') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="3">Total</th>
                                    <th class="fw-bold text-success">Rp
                                        {{ number_format($sortedTransaksis->sum('total_harga'), 0, ',', '.') }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-receipt"></i>
                        <h6>Belum Ada Transaksi</h6>
                        <p>Transaksi hari ini akan muncul di sini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Total Uang Per Metode Pembayaran -->
        <div class="rekap-card">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="bi bi-credit-card"></i>
                    Total Uang Per Metode Pembayaran
                </h6>

                @if ($operasional && $operasional->transaksis->count() > 0)
                        @php
                            $paymentTotals = [];
                            foreach ($operasional->transaksis as $transaksi) {
                                $method = $transaksi->metode_pembayaran;
                                if (!isset($paymentTotals[$method])) {
                                    $paymentTotals[$method] = ['donat' => 0, 'harga' => 0];
                                }
                                $paymentTotals[$method]['donat'] += $transaksi->total_donat;
                                $paymentTotals[$method]['harga'] += $transaksi->total_harga;
                            }

                            $paymentMethodsOrder = ['tunai', 'qris', 'transfer', 'grabfood', 'gofood'];
                            $orderedPaymentTotals = [];
                            foreach ($paymentMethodsOrder as $method) {
                                if (isset($paymentTotals[$method])) {
                                    $orderedPaymentTotals[$method] = $paymentTotals[$method];
                                } else {
                                    $orderedPaymentTotals[$method] = ['donat' => 0, 'harga' => 0];
                                }
                            }
                        @endphp

                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>Metode</th>
                                        <th>Total Transaksi</th>
                                        <th>Total Uang</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderedPaymentTotals as $method => $totals)
                                                    <tr>
                                                        <td>
                                                            <span class="badge bg-secondary payment-method-badge">
                                                                {{ ucfirst($method) }}
                                                            </span>
                                                        </td>
                                                        <td class="fw-semibold">
                                                            {{
                                        $operasional->transaksis->where('metode_pembayaran', $method)->count()
                                                                        }}
                                                        </td>
                                                        <td class="fw-semibold text-success">Rp
                                                            {{ number_format($totals['harga'], 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total</th>
                                        <th class="fw-bold">
                                            {{
                    $operasional->transaksis->count()
                                                    }}
                                        </th>
                                        <th class="fw-bold text-success">Rp
                                            {{ number_format(array_sum(array_column($paymentTotals, 'harga')), 0, ',', '.') }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-credit-card"></i>
                        <h6>Belum Ada Data Pembayaran</h6>
                        <p>Data pembayaran akan muncul setelah ada transaksi.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stok Produk -->
        <div class="rekap-card">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="bi bi-box-seam"></i>
                    Sisa Produk
                </h6>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Kemasan</th>
                                <th>Penggunaan</th>
                                <th>Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><span class="badge bg-primary payment-method-badge">Mika</span></td>
                                <td class="fw-semibold text-danger">{{ $usedMika }}</td>
                                <td class="fw-semibold text-success">{{ $totalMika }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary payment-method-badge">Dus 1</span></td>
                                <td class="fw-semibold text-danger">{{ $usedDus1 }}</td>
                                <td class="fw-semibold text-success">{{ $totalDus1 }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary payment-method-badge">Dus 2</span></td>
                                <td class="fw-semibold text-danger">{{ $usedDus2 }}</td>
                                <td class="fw-semibold text-success">{{ $totalDus2 }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary payment-method-badge">Dus 3</span></td>
                                <td class="fw-semibold text-danger">{{ $usedDus3 }}</td>
                                <td class="fw-semibold text-success">{{ $totalDus3 }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary payment-method-badge">Box</span></td>
                                <td class="fw-semibold text-danger">{{ $usedBox }}</td>
                                <td class="fw-semibold text-success">{{ $totalBox }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-primary payment-method-badge">Box 12</span></td>
                                <td class="fw-semibold text-danger">{{ $usedBox12 }}</td>
                                <td class="fw-semibold text-success">{{ $totalBox12 }}</td>
                            </tr>
                            <tr>
                                <td><span class="badge bg-warning text-dark payment-method-badge">Lilin</span></td>
                                <td class="fw-semibold text-danger">{{ $usedLilin }}</td>
                                <td class="fw-semibold text-success">{{ $totalLilin }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Catatan Operasional -->
        <div class="rekap-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="section-title mb-0">
                        <i class="bi bi-journal-text"></i>
                        Catatan Operasional
                    </h6>
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#catatanModal">
                        <i class="bi bi-plus-circle me-1"></i>Tambah
                    </button>
                </div>

                @if (count($tempCatatan) > 0)
                    @php
                        $totalPemasukan = 0;
                        $totalPengeluaran = 0;
                        foreach ($tempCatatan as $catatan) {
                            if ($catatan['jenis'] === 'pemasukan') {
                                $totalPemasukan += $catatan['jumlah'];
                            } else {
                                $totalPengeluaran += $catatan['jumlah'];
                            }
                        }
                        $totalNet = $totalPemasukan - $totalPengeluaran;
                    @endphp
                    <div class="table-responsive">
                        <table class="table table-hover table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jenis</th>
                                    <th>Jumlah</th>
                                    <th>Catatan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($tempCatatan as $index => $catatan)
                                    <tr>
                                        <td class="fw-semibold">{{ $index + 1 }}</td>
                                        <td>
                                            @if ($catatan['jenis'] === 'pemasukan')
                                                <span class="badge bg-success payment-method-badge">
                                                    <i class="bi bi-plus-circle me-1"></i>Pemasukan
                                                </span>
                                            @else
                                                <span class="badge bg-danger payment-method-badge">
                                                    <i class="bi bi-dash-circle me-1"></i>Pengeluaran
                                                </span>
                                            @endif
                                        </td>
                                        <td
                                            class="fw-semibold {{ $catatan['jenis'] === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                            Rp {{ number_format($catatan['jumlah'], 0, ',', '.') }}
                                        </td>
                                        <td>{{ $catatan['catatan'] ?? '-' }}</td>
                                        <td>
                                            <button class="btn btn-outline-danger btn-action" onclick="hapusCatatan({{ $index }})">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada catatan operasional.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total Jumlah</th>
                                    <th class="fw-bold {{ $totalNet >= 0 ? 'text-success' : 'text-danger' }}">Rp
                                        {{ number_format($totalNet, 0, ',', '.') }}</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="bi bi-journal-x"></i>
                        <h6>Belum Ada Catatan</h6>
                        <p>Tambahkan catatan operasional untuk hari ini.</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Rekap Uang -->
        <div class="rekap-card">
            <div class="card-body">
                <h6 class="section-title">
                    <i class="bi bi-cash-stack"></i>
                    Rekap Uang
                </h6>

                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris Pemasukan Penjualan -->
                            <tr>
                                <td class="fw-semibold">1</td>
                                <td>
                                    <span class="badge bg-success">
                                        <i class="bi bi-plus-circle me-1"></i>Pemasukan
                                    </span>
                                </td>
                                <td class="fw-semibold text-success">Rp
                                    {{ number_format($totalUangPenjualan, 0, ',', '.') }}
                                </td>
                                <td>Penjualan</td>
                            </tr>

                            <!-- Baris Catatan Operasional -->
                            @forelse ($tempCatatan as $index => $catatan)
                                <tr>
                                    <td class="fw-semibold">{{ $index + 2 }}</td>
                                    <td>
                                        @if ($catatan['jenis'] === 'pemasukan')
                                            <span class="badge bg-success">
                                                <i class="bi bi-plus-circle me-1"></i>Pemasukan
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="bi bi-dash-circle me-1"></i>Pengeluaran
                                            </span>
                                        @endif
                                    </td>
                                    <td
                                        class="fw-semibold {{ $catatan['jenis'] === 'pemasukan' ? 'text-success' : 'text-danger' }}">
                                        Rp {{ number_format($catatan['jumlah'], 0, ',', '.') }}
                                    </td>
                                    <td>{{ $catatan['catatan'] ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada catatan operasional
                                        tambahan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2">Total Jumlah</th>
                                <th class="fw-bold text-success">
                                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                </th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Total Pendapatan Highlight -->
        <div class="total-highlight">
            <div class="total-amount">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
            <div class="total-label">Total Pendapatan Hari Ini</div>
        </div>
        <div class="total-highlight total-highlight--muted">
            <div class="total-amount">
                Rp {{ number_format($cashDiPegawai, 0, ',', '.') }}
            </div>
            <div class="total-label">Cash Di Pegawai</div>
        </div>

        <!-- Save Report Section -->
        <div class="save-section">
            <h2 class="ui-section__title" style="justify-content:center;">Simpan Laporan Rekap Harian</h2>
            <p class="mb-4" style="color: var(--muted); font-size: 0.9rem;">
                Pastikan semua data sudah benar sebelum menyimpan laporan rekap harian.
            </p>
            <form action="{{ route('pegawai.simpan-rekap') }}" method="POST">
                @csrf
                <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                <input type="hidden" name="operasional_id" value="{{ $operasional->id }}">
                <input type="hidden" name="sisa_mika" value="{{ $totalMika }}">
                <input type="hidden" name="sisa_dus1" value="{{ $totalDus1 }}">
                <input type="hidden" name="sisa_dus2" value="{{ $totalDus2 }}">
                <input type="hidden" name="sisa_dus3" value="{{ $totalDus3 }}">
                <input type="hidden" name="sisa_box" value="{{ $totalBox }}">
                <input type="hidden" name="sisa_box12" value="{{ $totalBox12 }}">
                <input type="hidden" name="sisa_lilin" value="{{ $totalLilin }}">
                <input type="hidden" name="total_uang" value="{{ $totalPendapatan }}">

                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="bi bi-check-circle me-2"></i>
                    Simpan Laporan Rekap Harian
                </button>
            </form>
        </div>
    </div>

    <!-- Modal Tambah Catatan -->
    <div class="modal fade" id="catatanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Catatan Operasional</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="catatanForm" action="{{ route('pegawai.tambah-catatan') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Jenis</label>
                            <div class="btn-group w-100" role="group" aria-label="Jenis">
                                <input type="radio" class="btn-check" name="jenis" value="pemasukan" id="pemasukan" checked
                                    required>
                                <label class="btn btn-outline-success" for="pemasukan"><i
                                        class="bi bi-plus-circle me-1"></i>Pemasukan</label>
                                <input type="radio" class="btn-check" name="jenis" value="pengeluaran" id="pengeluaran">
                                <label class="btn btn-outline-danger" for="pengeluaran"><i
                                        class="bi bi-dash-circle me-1"></i>Pengeluaran</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah" class="form-label">Jumlah (Rp)</label>
                            <input type="text" class="form-control thousand-input" name="jumlah" inputmode="numeric"
                                autocomplete="off" required>
                        </div>
                        <div class="mb-3">
                            <label for="catatan" class="form-label">Catatan</label>
                            <textarea class="form-control" name="catatan" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        // Util: format dan bersihkan angka bertitik
        function sanitizeNumber(value) {
            if (typeof value !== 'string') return value;
            return value.replace(/\./g, '').replace(/[^0-9-]/g, '');
        }

        function formatThousand(value) {
            const negative = /^-/.test(value);
            const digitsOnly = value.replace(/[^0-9]/g, '');
            if (!digitsOnly) return '';
            const withDots = digitsOnly.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            return negative ? '-' + withDots : withDots;
        }

        function attachThousandFormatter(input) {
            if (!input) return;
            input.addEventListener('input', function() {
                const caret = this.selectionStart;
                const before = this.value;
                const cleaned = before.replace(/\./g, '');
                const formatted = formatThousand(cleaned);
                this.value = formatted;
                // Best effort caret handling: move to end
                // this.setSelectionRange(this.value.length, this.value.length);
            });
        }

        // Inisialisasi formatter pada input jumlah di modal
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.thousand-input');
            inputs.forEach(input => attachThousandFormatter(input));
        });

        document.getElementById('catatanForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            // Pastikan jumlah tanpa titik saat dikirim
            const jml = formData.get('jumlah');
            formData.set('jumlah', sanitizeNumber(jml || ''));

            fetch('{{ route('pegawai.tambah-catatan') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(JSON.stringify(err.error))
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('catatanModal')).hide();
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Gagal menambahkan catatan'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    let errorMessage = 'Gagal menambahkan catatan';
                    try {
                        const parsedError = JSON.parse(error.message);
                        errorMessage = Object.values(parsedError).flat().join(', ');
                    } catch (e) {
                        errorMessage = error.message;
                    }
                    alert('Gagal menambahkan catatan: ' + errorMessage);
                });
        });

        function hapusCatatan(index) {
            if (confirm('Hapus catatan ini?')) {
                fetch('{{ route('pegawai.hapus-catatan') }}', {
                        method: 'POST',
                        body: JSON.stringify({
                            index: index
                        }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            return response.json().then(err => {
                                throw new Error(err.error || 'Gagal menghapus catatan')
                            });
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error: ' + (data.error || 'Gagal menghapus catatan'));
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal menghapus catatan: ' + error.message);
                    });
            }
        }
    </script>
@endsection