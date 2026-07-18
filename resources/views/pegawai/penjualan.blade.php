@extends('layouts.app')

@section('title', 'Penjualan - {{ $outlet->nama }}')

@section('css')
    <style>
        .stok-info {
            background: #fff;
            border: 1px solid var(--border);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 1.15rem;
        }

        .stok-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(80px, 1fr));
            gap: 0.5rem;
        }

        .stok-item {
            text-align: center;
            padding: 0.5rem;
            background: #f8f9fc;
            border-radius: 8px;
            border: 1px solid var(--border);
        }

        .stok-label {
            font-size: 0.72rem;
            color: var(--muted);
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .stok-value {
            font-size: 1rem;
            font-weight: 650;
            color: var(--dark-color);
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }

        .fixed-bottom-btn-container {
            position: fixed;
            bottom: 0;
            left: 0;
            width: 100%;
            background: white;
            padding: 0.75rem 1rem;
            box-shadow: 0 -2px 8px rgba(15, 23, 42, 0.08);
            z-index: 1050;
            text-align: center;
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
            opacity: 0.9;
        }

        @media (max-width: 768px) {
            body {
                padding-bottom: 80px;
            }
        }

        @media (max-width: 576px) {
            .stok-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .table-responsive {
                font-size: 0.85rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .btn-action {
                font-size: 0.75rem;
                padding: 0.2rem 0.4rem;
            }

            .table td:nth-child(2) {
                max-width: 120px;
                word-wrap: break-word;
                white-space: normal;
                vertical-align: top;
            }

            .table td:nth-child(2) .badge {
                display: inline-block;
                margin: 1px 2px 1px 0;
                font-size: 0.7rem;
                padding: 0.25em 0.4em;
                white-space: nowrap;
                line-height: 1.2;
            }

            .table {
                min-width: 600px;
            }
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .summary-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            border: 1px solid var(--border);
        }

        .summary-value {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .summary-label {
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
        }

        .rekap-card {
            background: white;
            border-radius: 8px;
            border: 1px solid var(--border);
            margin-bottom: 1rem;
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
        }

        .payment-method-badge {
            font-size: 0.75rem;
            padding: 0.25em 0.5em;
        }

        @media (max-width: 768px) {
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }

            .modal-content {
                border-radius: 8px;
            }

            .modal-header {
                padding: 0.75rem 1rem;
            }

            .modal-title {
                font-size: 1rem;
            }

            .modal-body {
                padding: 1rem;
            }

            .summary-grid {
                grid-template-columns: 1fr;
                gap: 0.75rem;
                margin-bottom: 1rem;
            }

            .summary-card {
                padding: 0.75rem;
            }

            .summary-value {
                font-size: 1.1rem;
            }

            .summary-label {
                font-size: 0.75rem;
            }

            .rekap-card {
                margin-bottom: 0.75rem;
            }

            .rekap-card .card-body {
                padding: 0.75rem;
            }

            .section-title {
                font-size: 0.85rem;
                margin-bottom: 0.5rem;
            }

            .table-responsive {
                font-size: 0.8rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th,
            .table td {
                padding: 0.4rem 0.2rem;
                white-space: nowrap;
            }

            .payment-method-badge {
                font-size: 0.7rem;
                padding: 0.2em 0.4em;
            }

            .modal-footer {
                padding: 0.75rem 1rem;
            }
        }

        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0.25rem;
                max-width: calc(100% - 0.5rem);
            }

            .summary-grid {
                gap: 0.5rem;
            }

            .summary-card {
                padding: 0.5rem;
            }

            .summary-value {
                font-size: 1rem;
            }

            .summary-label {
                font-size: 0.7rem;
            }

            .rekap-card .card-body {
                padding: 0.5rem;
            }

            .table-responsive {
                font-size: 0.75rem;
            }

            .table th,
            .table td {
                padding: 0.3rem 0.15rem;
                white-space: nowrap;
                font-size: 0.7rem;
            }

            .payment-method-badge {
                font-size: 0.75rem;
                padding: 0.1em 0.25em;
            }

            .table th:nth-child(1),
            .table td:nth-child(1) {
                min-width: 40px;
            }

            .table th:nth-child(2),
            .table td:nth-child(2) {
                min-width: 60px;
            }

            .table th:nth-child(3),
            .table td:nth-child(3) {
                min-width: 50px;
            }

            .table th:nth-child(4),
            .table td:nth-child(4) {
                min-width: 80px;
            }
        }
    </style>
@endsection

@section('back-button')
    <a href="{{ route('pegawai.dashboard') }}" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    <div class="ui-page ui-page--wide">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <header class="ui-header">
            <div>
                <h1>{{ $outlet->nama }}</h1>
                <p><i class="bi bi-geo-alt me-1"></i>{{ $outlet->alamat }}</p>
            </div>
            @if ($operasional)
                <div class="ui-header__meta">
                    <span class="ui-chip {{ $operasional->status === 'aktif' ? 'ui-chip--green' : 'ui-chip--rose' }}">
                        {{ ucfirst($operasional->status) }}
                    </span>
                </div>
            @endif
        </header>

        @if ($operasional)
            <div class="ui-stats">
                <div class="ui-stat">
                    <span>Total Kloter</span>
                    <strong>{{ $totalKloter }}</strong>
                </div>
                <div class="ui-stat">
                    <span>Total Donat</span>
                    <strong>{{ $totalDonat }}</strong>
                </div>
                <div class="ui-stat">
                    <span>Donat Terjual</span>
                    <strong>{{ $transaksis->sum('total_donat') }}</strong>
                </div>
                <div class="ui-stat">
                    <span>Sisa Donat</span>
                    <strong>{{ $operasional->total_donat_harian }}</strong>
                </div>
            </div>

            <section class="ui-section">
                <h2 class="ui-section__title">Sisa Stok Kemasan</h2>
                <div class="stok-info">
                    <div class="stok-grid">
                        <div class="stok-item">
                            <div class="stok-label">Mika</div>
                            <div class="stok-value">{{ $stokOutlet->stok_mika }}</div>
                        </div>
                        <div class="stok-item">
                            <div class="stok-label">Dus 1</div>
                            <div class="stok-value">{{ $stokOutlet->stok_dus1 }}</div>
                        </div>
                        <div class="stok-item">
                            <div class="stok-label">Dus 2</div>
                            <div class="stok-value">{{ $stokOutlet->stok_dus2 }}</div>
                        </div>
                        <div class="stok-item">
                            <div class="stok-label">Dus 3</div>
                            <div class="stok-value">{{ $stokOutlet->stok_dus3 }}</div>
                        </div>
                        <div class="stok-item">
                            <div class="stok-label">Box</div>
                            <div class="stok-value">{{ $stokOutlet->stok_box }}</div>
                        </div>
                        <div class="stok-item">
                            <div class="stok-label">Box 12</div>
                            <div class="stok-value">{{ $stokOutlet->stok_box12 }}</div>
                        </div>
                        <div class="stok-item">
                            <div class="stok-label">Lilin</div>
                            <div class="stok-value">{{ $stokOutlet->stok_lilin ?? 0 }}</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kloterModal">
                            <i class="bi bi-eye me-1"></i>Detail Kloter
                        </button>
                    </div>
                </div>
            </section>

            @if ($rekap)
                <div class="alert alert-info mb-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i>Penjualan hari ini telah selesai dan Rekap telah dibuat.
                </div>
                <div class="mb-3">
                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#rekapModal">
                        <i class="bi bi-eye me-2"></i>Lihat Rekap
                    </button>
                </div>
            @else
                <a href="{{ route('pegawai.transaksi', $outlet->id) }}" class="ui-primary d-none d-md-flex">
                    <div class="ui-primary__icon"><i class="bi bi-cart-plus"></i></div>
                    <div class="ui-primary__body">
                        <strong>Tambah Transaksi</strong>
                        <span>Catat penjualan pelanggan</span>
                    </div>
                    <i class="bi bi-arrow-right"></i>
                </a>
                <div class="mb-3 d-none d-md-block">
                    <a href="{{ route('pegawai.rekap', $outlet->id) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-file-earmark-text me-2"></i>Rekap Harian
                    </a>
                </div>
                <div class="d-md-none mb-3">
                    <a href="{{ route('pegawai.rekap', $outlet->id) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-file-earmark-text me-2"></i>Rekap Harian
                    </a>
                </div>
                <div class="fixed-bottom-btn-container d-md-none">
                    <a href="{{ route('pegawai.transaksi', $outlet->id) }}" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Transaksi
                    </a>
                </div>
            @endif

            <section class="ui-section">
                <h2 class="ui-section__title">Daftar Transaksi Hari Ini</h2>

                    @if(count($transaksis) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Transaksi</th>
                                        <th>Item</th>
                                        <th>Pembayaran</th>
                                        <th>Total</th>
                                        <th>Waktu</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($transaksis as $index => $transaksi)
                                        <tr>
                                            <td class="fw-semibold">#{{ $transaksi->no_transaksi ?? $transaksi->id }}</td>
                                            <td>
                                                @php
                                                    $itemGroups = [];
                                                    foreach ($transaksi->items as $item) {
                                                        $kemasan = $item->kemasan;
                                                        $jumlah = $item->jumlah;

                                                        // Mapping kemasan ke kode singkat
                                                        $kodeKemasan = '';
                                                        switch ($kemasan) {
                                                            case 'mika':
                                                                $kodeKemasan = 'M';
                                                                break;
                                                            case 'dus1':
                                                                $kodeKemasan = 'D1';
                                                                break;
                                                            case 'dus2':
                                                                $kodeKemasan = 'D2';
                                                                break;
                                                            case 'dus3':
                                                                $kodeKemasan = 'D3';
                                                                break;
                                                            case 'box':
                                                                $kodeKemasan = 'B';
                                                                break;
                                                            case 'box12':
                                                                $kodeKemasan = 'B12';
                                                                break;
                                                            case 'lilin':
                                                                $kodeKemasan = 'L';
                                                                break;
                                                            default:
                                                                $kodeKemasan = strtoupper($kemasan);
                                                        }

                                                        if (!isset($itemGroups[$kodeKemasan])) {
                                                            $itemGroups[$kodeKemasan] = 0;
                                                        }
                                                        $itemGroups[$kodeKemasan] += $jumlah;
                                                    }
                                                @endphp
                                                @if(count($itemGroups) > 0)
                                                    @foreach ($itemGroups as $kode => $total)
                                                        <span class="badge bg-info me-1 mb-1">{{ $kode }} ({{ $total }})</span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ ucfirst($transaksi->metode_pembayaran) }}</span>
                                            </td>
                                            <td class="text-success fw-semibold">Rp
                                                {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                            <td class="text-muted small">{{ $transaksi->created_at->format('H:i:s') }}</td>
                                            <td>
                                                <a href="{{ route('pegawai.transaksi.detail', [$outlet->id, $transaksi->id]) }}"
                                                    class="btn btn-outline-info btn-action" title="Detail">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if (!$rekap)
                                                    <form action="{{ route('pegawai.transaksi.delete', [$outlet->id, $transaksi->id]) }}"
                                                        method="POST" class="d-inline ms-1 delete-transaksi-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-action"
                                                            onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?')"
                                                            title="Hapus">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center py-4">
                                                <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                                <p class="text-muted mt-2 mb-0">Belum ada transaksi hari ini.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                            <h6 class="text-muted mt-3">Belum ada transaksi</h6>
                            <p class="text-muted">Transaksi hari ini akan muncul di sini.</p>
                        </div>
                    @endif
            </section>
        @endif
    </div>

    <!-- Modal Detail Kloter -->
    <div class="modal fade" id="kloterModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kloter - {{ $outlet->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>Daftar Kloter</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Jumlah Donat</th>
                                    <th>Mika</th>
                                    <th>Dus 1</th>
                                    <th>Dus 2</th>
                                    <th>Dus 3</th>
                                    <th>Box</th>
                                    <th>Box 12</th>
                                    <th>Waktu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($kloters as $index => $kloter)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $kloter->jumlah_donat }}</td>
                                        <td>{{ $kloter->jumlah_mika }}</td>
                                        <td>{{ $kloter->jumlah_dus1 }}</td>
                                        <td>{{ $kloter->jumlah_dus2 }}</td>
                                        <td>{{ $kloter->jumlah_dus3 }}</td>
                                        <td>{{ $kloter->jumlah_box }}</td>
                                        <td>{{ $kloter->jumlah_box12 }}</td>
                                        <td>{{ $kloter->created_at->format('H:i:s') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">Belum ada kloter hari ini.</td>
                                    </tr>
                                @endforelse
                                @if ($kloters->count() > 0)
                                    <tr class="table-light">
                                        <td><strong>Total</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_donat') }}</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_mika') }}</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_dus1') }}</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_dus2') }}</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_dus3') }}</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_box') }}</strong></td>
                                        <td><strong>{{ $kloters->sum('jumlah_box12') }}</strong></td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Detail Rekap -->
    @if ($rekap)
        <div class="modal fade" id="rekapModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Detail Rekap Harian - {{ $outlet->nama }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Status Validasi -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-semibold">Tanggal:</span>
                                <span>{{ \Carbon\Carbon::parse($rekap->tanggal)->format('d-m-Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-semibold">Status Validasi:</span>
                                @if($rekap->status === 'validated')
                                    <span class="status-validated">
                                        <i class="bi bi-check-circle-fill me-1"></i>Divalidasi
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="bi bi-clock me-1"></i>Menunggu Validasi
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2">
                                <span class="fw-semibold">Waktu:</span>
                                <span class="text-muted small">
                                    @if($rekap->status === 'validated')
                                        Divalidasi: {{ $rekap->updated_at->format('d/m/Y H:i') }}
                                    @else
                                        Dibuat: {{ $rekap->created_at->format('d/m/Y H:i') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="summary-grid mb-4">
                            <div class="summary-card">
                                <div class="summary-value text-success">Rp
                                    {{ number_format($rekap->total_uang_penjualan ?? 0, 0, ',', '.') }}</div>
                                <div class="summary-label">Total Uang Penjualan</div>
                            </div>
                            <div class="summary-card bg-success">
                                <div class="summary-value" style="color: white;">Rp
                                    {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}</div>
                                <div class="summary-label" style="color: white;">Total Pendapatan Hari Ini</div>
                            </div>
                            <div class="summary-card bg-secondary">
                                <div class="summary-value" style="color: white;">
                                    @php
                                        $cashView = $rekap->cash_di_pegawai
                                            ?? (($rekap->total_tunai ?? 0) + ($rekap->operasional?->catatan_oprasional_sum_jumlah ?? 0));
                                    @endphp
                                    Rp {{ number_format($cashView, 0, ',', '.') }}
                                </div>
                                <div class="summary-label" style="color: white;">Cash Di Pegawai</div>
                            </div>
                        </div>

                        <!-- Tabel Donat -->
                        <div class="rekap-card mb-4">
                            <div class="card-body p-2">
                                <h6 class="section-title mb-2">
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
                                                <td class="fw-semibold">{{ $rekap->total_donat_terjual ?? 0 }}</td>
                                                <td class="fw-semibold">{{ $totalDonat - ($rekap->total_donat_terjual ?? 0) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Transaksi -->
                        <div class="rekap-card mb-4">
                            <div class="card-body p-2">
                                <h6 class="section-title mb-2">
                                    <i class="bi bi-receipt"></i>
                                    Daftar Transaksi
                                </h6>
                                @if ($rekap->operasional && $rekap->operasional->transaksis && $rekap->operasional->transaksis->count() > 0)
                                    @php
                                        // Sort transaksis by created_at descending (latest first)
                                        $sortedTransaksis = $rekap->operasional->transaksis->sortByDesc('created_at')->values();

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
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Transaksi</th>
                                                    <th>Item</th>
                                                    <th>Metode</th>
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
                                                            @php
                                                                $iconMap = [
                                                                    'tunai' => 'bi-cash',
                                                                    'qris' => 'bi-qr-code',
                                                                    'transfer' => 'bi-bank',
                                                                    'grabfood' => 'bi-bag-check',
                                                                    'gofood' => 'bi-bag-heart'
                                                                ];
                                                                $metode = strtolower($transaksi->metode_pembayaran);
                                                                $icon = $iconMap[$metode] ?? 'bi-credit-card';
                                                            @endphp
                                                            <span class="badge bg-secondary payment-method-badge">
                                                                <i class="bi {{ $icon }} me-1"></i>
                                                                {{ ucfirst($transaksi->metode_pembayaran) }}
                                                            </span>
                                                        </td>
                                                        <td class="fw-semibold text-success">Rp
                                                            {{ number_format($transaksi->total_harga, 0, ',', '.') }}</td>
                                                        <td>{{ $transaksi->created_at ? $transaksi->created_at->format('H:i:s') : '-' }}
                                                        </td>
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
                                    <div class="empty-state text-center py-4">
                                        <i class="bi bi-receipt" style="font-size:2rem;"></i>
                                        <h6>Belum Ada Transaksi</h6>
                                        <p class="mb-0">Transaksi hari ini akan muncul di sini.</p>
                                    </div>
                                @endif
                            </div>
                        </div>


                        <!-- Tabel Total Uang Per Metode Pembayaran -->
                        <div class="rekap-card mb-4">
                            <div class="card-body p-2">
                                <h6 class="section-title mb-2">
                                    <i class="bi bi-credit-card"></i>
                                    Total Uang Per Metode Pembayaran
                                </h6>
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
                                            @php
                                                $paymentMethods = [
                                                    'tunai' => ['label' => 'Tunai', 'total_transaksi' => 0, 'total_uang' => 0],
                                                    'qris' => ['label' => 'QRIS', 'total_transaksi' => 0, 'total_uang' => 0],
                                                    'transfer' => ['label' => 'Transfer', 'total_transaksi' => 0, 'total_uang' => 0],
                                                    'grabfood' => ['label' => 'GrabFood', 'total_transaksi' => 0, 'total_uang' => 0],
                                                    'gofood' => ['label' => 'GoFood', 'total_transaksi' => 0, 'total_uang' => 0]
                                                ];

                                                // Hitung total transaksi dan total uang per metode pembayaran dari transaksi
                                                if ($rekap->operasional && $rekap->operasional->transaksis) {
                                                    foreach ($rekap->operasional->transaksis as $transaksi) {
                                                        $method = $transaksi->metode_pembayaran;
                                                        if (isset($paymentMethods[$method])) {
                                                            $paymentMethods[$method]['total_transaksi'] += 1;
                                                            $paymentMethods[$method]['total_uang'] += $transaksi->total_harga;
                                                        }
                                                    }
                                                }
                                            @endphp
                                            @foreach ($paymentMethods as $method => $data)
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-secondary payment-method-badge">
                                                            @php
                                                                $iconMap = [
                                                                    'tunai' => 'bi-cash-coin',
                                                                    'qris' => 'bi-qr-code',
                                                                    'transfer' => 'bi-bank',
                                                                    'grabfood' => 'bi-bag-check',
                                                                    'gofood' => 'bi-bag-heart'
                                                                ];
                                                                $iconClass = $iconMap[$method] ?? 'bi-credit-card';
                                                            @endphp
                                                            <i class="bi {{ $iconClass }} me-1"></i>{{ $data['label'] }}
                                                        </span>
                                                    </td>
                                                    <td class="fw-semibold">{{ $data['total_transaksi'] }}</td>
                                                    <td class="fw-semibold text-success">Rp
                                                        {{ number_format($data['total_uang'], 0, ',', '.') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="table-dark">
                                            <tr>
                                                <th>Total</th>
                                                <th class="fw-bold">
                                                    {{
                $operasional->transaksis->count()
                                                }}
                                                </th>
                                                <th class="fw-bold text-success">Rp
                                                    {{ number_format(array_sum(array_column($paymentMethods, 'total_uang')), 0, ',', '.') }}
                                                </th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Sisa Produk -->
                        <div class="rekap-card mb-4">
                            <div class="card-body p-2">
                                <h6 class="section-title mb-2">
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
                                                <td><span class="badge bg-primary payment-method-badge"><i
                                                            class="bi bi-box me-2"></i>Mika</span></td>
                                                <td class="fw-semibold text-danger">{{ $rekap->used_mika ?? 0 }}</td>
                                                <td class="fw-semibold text-success">{{ $rekap->sisa_mika ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-primary payment-method-badge"><i
                                                            class="bi bi-box me-2"></i>Dus 1</span></td>
                                                <td class="fw-semibold text-danger">{{ $rekap->used_dus1 ?? 0 }}</td>
                                                <td class="fw-semibold text-success">{{ $rekap->sisa_dus1 ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-primary payment-method-badge"><i
                                                            class="bi bi-box me-2"></i>Dus 2</span></td>
                                                <td class="fw-semibold text-danger">{{ $rekap->used_dus2 ?? 0 }}</td>
                                                <td class="fw-semibold text-success">{{ $rekap->sisa_dus2 ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-primary payment-method-badge"><i
                                                            class="bi bi-box me-2"></i>Dus 3</span></td>
                                                <td class="fw-semibold text-danger">{{ $rekap->used_dus3 ?? 0 }}</td>
                                                <td class="fw-semibold text-success">{{ $rekap->sisa_dus3 ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-primary payment-method-badge"><i
                                                            class="bi bi-box me-2"></i>Box</span></td>
                                                <td class="fw-semibold text-danger">{{ $rekap->used_box ?? 0 }}</td>
                                                <td class="fw-semibold text-success">{{ $rekap->sisa_box ?? 0 }}</td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge bg-warning text-dark payment-method-badge"><i
                                                            class="bi bi-lightbulb me-2"></i>Lilin</span></td>
                                                <td class="fw-semibold text-danger">{{ $rekap->used_lilin ?? 0 }}</td>
                                                <td class="fw-semibold text-success">{{ $rekap->sisa_lilin ?? 0 }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Catatan Operasional -->
                        <div class="rekap-card mb-4">
                            <div class="card-body p-2">
                                <h6 class="section-title mb-2">
                                    <i class="bi bi-journal-text"></i>
                                    Catatan Operasional
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
                                            @forelse ($rekap->catatanOperasionals ?? [] as $index => $catatan)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        @if ($catatan->jenis === 'pemasukan')
                                                            <span class="badge bg-success"><i
                                                                    class="bi bi-plus-circle me-1"></i>Pemasukan</span>
                                                        @else
                                                            <span class="badge bg-danger"><i
                                                                    class="bi bi-dash-circle me-1"></i>Pengeluaran</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="{{ $catatan->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }} fw-semibold">
                                                        Rp {{ number_format($catatan->jumlah, 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ $catatan->catatan ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Belum ada catatan operasional
                                                        tambahan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-dark">
                                            <tr>
                                                <th colspan="2">Total</th>
                                                @php
                                                    $netCatatan = 0;
                                                    foreach (($rekap->catatanOperasionals ?? []) as $catatan) {
                                                        if ($catatan->jenis === 'pemasukan') {
                                                            $netCatatan += (int) $catatan->jumlah;
                                                        } elseif ($catatan->jenis === 'pengeluaran') {
                                                            $netCatatan -= (int) $catatan->jumlah;
                                                        }
                                                    }
                                                @endphp
                                                <th class="fw-bold {{ $netCatatan >= 0 ? 'text-success' : 'text-danger' }}">Rp
                                                    {{ number_format($netCatatan, 0, ',', '.') }}</th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Tabel Rekap Uang -->
                        <div class="rekap-card mb-2">
                            <div class="card-body p-2">
                                <h6 class="section-title mb-2">
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
                                            @php
                                                $totalJumlah = (int) ($rekap->total_uang_penjualan ?? 0);
                                            @endphp
                                            <tr>
                                                <td>1</td>
                                                <td>
                                                    <span class="badge bg-success"><i
                                                            class="bi bi-plus-circle me-1"></i>Pemasukan</span>
                                                </td>
                                                <td class="text-success fw-semibold">Rp
                                                    {{ number_format($rekap->total_uang_penjualan ?? 0, 0, ',', '.') }}</td>
                                                <td>Penjualan</td>
                                            </tr>
                                            @forelse ($rekap->catatanOperasionals ?? [] as $index => $catatan)
                                                @php
                                                    $totalJumlah += (int) $catatan->jumlah;
                                                @endphp
                                                <tr>
                                                    <td>{{ $index + 2 }}</td>
                                                    <td>
                                                        @if ($catatan->jenis === 'pemasukan')
                                                            <span class="badge bg-success"><i
                                                                    class="bi bi-plus-circle me-1"></i>Pemasukan</span>
                                                        @else
                                                            <span class="badge bg-danger"><i
                                                                    class="bi bi-dash-circle me-1"></i>Pengeluaran</span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="{{ $catatan->jenis === 'pemasukan' ? 'text-success' : 'text-danger' }} fw-semibold">
                                                        Rp {{ number_format($catatan->jumlah, 0, ',', '.') }}
                                                    </td>
                                                    <td>{{ $catatan->catatan ?? '-' }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted">Belum ada catatan operasional
                                                        tambahan.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot class="table-dark">
                                            <tr>
                                                <th colspan="2">Total Jumlah</th>
                                                <th class="fw-bold text-success">Rp
                                                    {{ number_format($rekap->total_uang, 0, ',', '.') }}</th>
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
                                Rp {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="total-label">Total Pendapatan Hari Ini</div>
                        </div>
                        <div class="total-highlight total-highlight--muted">
                            @php
                                $cashViewBottom = $rekap->cash_di_pegawai
                                    ?? (($rekap->total_tunai ?? 0) + ($rekap->operasional?->catatan_oprasional_sum_jumlah ?? 0));
                            @endphp
                            <div class="total-amount">
                                Rp {{ number_format($cashViewBottom, 0, ',', '.') }}
                            </div>
                            <div class="total-label">Cash Di Pegawai</div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @forelse ($rekap->operasional->transaksis ?? [] as $transaksi)
            <div class="modal fade nested-modal" id="transaksiModal{{ $transaksi->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static" style="z-index: 1055;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Transaksi -
                                {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d-m-Y H:i:s') }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->metode_pembayaran) }}</p>
                            <p><strong>Total Donat:</strong> {{ $transaksi->total_donat }}</p>
                            <p><strong>Total Harga:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}</p>
                            <h6>Detail Items</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Kemasan</th>
                                            <th>Jumlah</th>
                                            <th>Donat</th>
                                            <th>Jenis</th>
                                            <th>Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $totalJumlah = 0;
                                            $totalDonat = 0;
                                            $totalHarga = 0;
                                        @endphp
                                        @forelse ($transaksi->items as $index => $item)
                                            <tr>
                                                <td>{{ $index + 1 }}</td>
                                                <td>{{ ucfirst($item->kemasan) }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ $item->jumlah }}</td>
                                                <td>{{ ucfirst($item->tipe) }}</td>
                                                <td>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                            </tr>
                                            @php
                                                $totalJumlah += $item->jumlah;
                                                $totalDonat += $item->jumlah;
                                                $totalHarga += $item->subtotal;
                                            @endphp
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">Belum ada item.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    @if ($transaksi->items->count() > 0)
                                        <tfoot class="table-dark">
                                            <tr>
                                                <th colspan="2">Total</th>
                                                <th>{{ $totalJumlah }}</th>
                                                <th>{{ $totalDonat }}</th>
                                                <th>-</th>
                                                <th>Rp {{ number_format($totalHarga, 0, ',', '.') }}</th>
                                            </tr>
                                        </tfoot>
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
        @endforelse
    @endif
@endsection