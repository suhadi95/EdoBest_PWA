@extends('layouts.app')

@section('title', 'Histori Rekap')

@section('css')
    <style>
        .rekap-card {
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            transition: transform 0.2s;
        }

        .rekap-card:hover {
            transform: translateY(-2px);
        }

        .status-validated {
            color: #198754;
            font-weight: 600;
        }

        .rekap-amount {
            font-size: 1.1rem;
            font-weight: 700;
            color: #198754;
        }

        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
        }

        .btn-detail {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
        }

        .rekap-summary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .summary-title {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .summary-value {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .outlet-info {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .outlet-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        /* CSS untuk Modal Rekap */
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
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
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

        .rekap-card-modal {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
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

        .total-highlight {
            background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
            color: #1565c0;
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            text-align: center;
            margin: 1rem 0;
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

        /* Mobile responsive untuk modal rekap */
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

            .rekap-card-modal {
                margin-bottom: 0.75rem;
            }

            .rekap-card-modal .card-body {
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
            .rekap-card .card-body {
                padding: 1rem;
            }

            .table-responsive {
                font-size: 0.85rem;
                overflow-x: auto;
            }

            .btn-detail {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            .rekap-summary {
                padding: 1rem;
            }

            .summary-value {
                font-size: 1.25rem;
            }

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

            .rekap-card-modal .card-body {
                padding: 0.5rem;
            }

            .section-title {
                font-size: 1.5rem;
            }

            .table-responsive {
                font-size: 0.75rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }

            .table th,
            .table td {
                padding: 0.3rem 0.15rem;
                white-space: nowrap;
                font-size: 0.7rem;
            }

            .payment-method-badge {
                font-size: 0.65rem;
                padding: 0.1em 0.25em;
            }

            /* Optimasi khusus untuk tabel dengan banyak kolom */
            .table {
                min-width: 100%;
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

        /* Pagination - Sesuai Tema Website */
        .pagination-wrapper {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            margin-bottom: 1rem;
        }

        .pagination-wrapper .pagination {
            margin: 0;
        }

        .pagination-wrapper .page-item {
            margin: 0 0.25rem;
        }

        .pagination-wrapper .page-link {
            border-radius: 8px;
            border: 1px solid #dee2e6;
            color: #495057;
            padding: 0.625rem 0.875rem;
            transition: all 0.2s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            font-weight: 500;
        }

        .pagination-wrapper .page-link:hover {
            color: #198754;
            background-color: #f8f9fa;
            border-color: #198754;
            transform: translateY(-1px);
            box-shadow: 0 2px 6px rgba(25, 135, 84, 0.15);
        }

        .pagination-wrapper .page-item.active .page-link {
            color: #ffffff;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-color: #198754;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(25, 135, 84, 0.25);
        }

        .pagination-wrapper .page-item.active .page-link:hover {
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(25, 135, 84, 0.3);
        }

        .pagination-wrapper .page-item.disabled .page-link {
            color: #6c757d;
            background-color: #f8f9fa;
            border-color: #dee2e6;
            cursor: not-allowed;
            opacity: 0.6;
            pointer-events: none;
            box-shadow: none;
        }

        .pagination-wrapper .page-item.disabled .page-link:hover {
            transform: none;
        }

        /* Responsive Pagination */
        @media (max-width: 768px) {
            .pagination-wrapper {
                margin-top: 1.5rem;
                margin-bottom: 0.75rem;
            }

            .pagination-wrapper .pagination {
                flex-wrap: wrap;
            }

            .pagination-wrapper .page-item {
                margin: 0 0.2rem 0.5rem 0.2rem;
            }

            .pagination-wrapper .page-link {
                padding: 0.5rem 0.75rem;
                font-size: 0.875rem;
            }
        }

        @media (max-width: 576px) {
            .pagination-wrapper {
                margin-top: 1rem;
                margin-bottom: 0.5rem;
            }

            .pagination-wrapper .pagination {
                flex-wrap: wrap;
            }

            .pagination-wrapper .page-item {
                margin: 0 0.15rem 0.4rem 0.15rem;
            }

            .pagination-wrapper .page-link {
                padding: 0.45rem 0.65rem;
                font-size: 0.8rem;
            }
        }
    </style>
@endsection

@section('back-button')
    <a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    <div class="container-fluid px-2 px-md-3">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">
                <i class="bi bi-file-earmark-check text-primary me-2"></i>Histori Rekap
            </h4>
        </div>

        <!-- Alert Messages -->
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

        <!-- Outlet Information -->
        <div class="outlet-info">
            <div class="outlet-name">
                <i class="bi bi-shop me-2"></i>{{ $outlet->nama }}
            </div>
            <div class="outlet-address">
                <i class="bi bi-geo-alt me-1"></i>{{ $outlet->alamat }}
            </div>
        </div>


        <!-- Histori Rekap Table -->
        <div class="rekap-card card">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="bi bi-list-ul me-2"></i>Daftar Histori Rekap
                </h6>

                @if($historiRekap->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Total Pendapatan</th>
                                    <th>Jumlah Transaksi</th>
                                    <th>Status</th>
                                    <th>Divalidasi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($historiRekap as $rekap)
                                    <tr>
                                        <td class="fw-semibold">{{ \Carbon\Carbon::parse($rekap->tanggal)->format('d F Y') }}</td>
                                        <td>
                                            <span class="rekap-amount">Rp
                                                {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}</span>
                                            @php
                                                $cashList = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0);
                                            @endphp
                                            <div class="small text-muted">Cash: Rp {{ number_format($cashList, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $rekap->operasional->transaksis->count() ?? 0 }}
                                                transaksi</span>
                                        </td>
                                        <td>
                                            @if($rekap->status === 'validated')
                                                <span class="status-validated">
                                                    <i class="bi bi-check-circle-fill me-1"></i>Divalidasi
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>Menunggu Validasi
                                                </span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            @if($rekap->status === 'validated')
                                                {{ $rekap->updated_at->format('d/m/Y H:i') }}
                                            @else
                                                {{ $rekap->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-outline-primary btn-detail" data-bs-toggle="modal"
                                                    data-bs-target="#rekapModal{{ $rekap->id }}">
                                                    <i class="bi bi-eye me-1"></i>Detail
                                                </button>
                                                @if($rekap->status === 'pending' && (session('user') && session('user')->id === $rekap->pegawai_id))
                                                    <form action="{{ route('pegawai.rekap.delete', [$outlet->id, $rekap->id]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Hapus rekap ini? Tindakan tidak dapat dibatalkan.')"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-detail">
                                                            <i class="bi bi-trash3 me-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada histori rekap.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($historiRekap->hasPages())
                        <div class="pagination-wrapper">
                            <nav aria-label="Navigasi halaman histori rekap">
                                <ul class="pagination">
                                    <!-- Previous Page Link -->
                                    @if ($historiRekap->onFirstPage())
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                        </li>
                                    @else
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $historiRekap->previousPageUrl() }}" rel="prev">Previous</a>
                                        </li>
                                    @endif

                                    <!-- Pagination Elements -->
                                    @foreach ($historiRekap->getUrlRange(1, $historiRekap->lastPage()) as $page => $url)
                                        @if ($page == $historiRekap->currentPage())
                                            <li class="page-item active" aria-current="page">
                                                <a class="page-link" href="#">{{ $page }}</a>
                                            </li>
                                        @else
                                            <li class="page-item">
                                                <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                            </li>
                                        @endif
                                    @endforeach

                                    <!-- Next Page Link -->
                                    @if ($historiRekap->hasMorePages())
                                        <li class="page-item">
                                            <a class="page-link" href="{{ $historiRekap->nextPageUrl() }}" rel="next">Next</a>
                                        </li>
                                    @else
                                        <li class="page-item disabled">
                                            <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                                        </li>
                                    @endif
                                </ul>
                            </nav>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-check text-muted" style="font-size: 4rem;"></i>
                        <h6 class="text-muted mt-3">Belum ada histori rekap</h6>
                        <p class="text-muted">Histori rekap Anda akan muncul di sini setelah membuat rekap harian.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Detail Rekap untuk setiap rekap -->
    @foreach ($historiRekap as $rekap)
        <div class="modal fade" id="rekapModal{{ $rekap->id }}" tabindex="-1" aria-hidden="true">
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

                        <!-- Summary Cards -->
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
                                @php $cashModal = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0); @endphp
                                <div class="summary-value" style="color: white;">Rp {{ number_format($cashModal, 0, ',', '.') }}
                                </div>
                                <div class="summary-label" style="color: white;">Cash Di Pegawai</div>
                            </div>
                        </div>

                        <!-- Tabel Donat -->
                        <div class="rekap-card-modal mb-4">
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
                                                <td class="fw-semibold">
                                                    {{ $rekap->operasional->kloters->sum('jumlah_donat') ?? 0 }}</td>
                                                <td class="fw-semibold">{{ $rekap->total_donat_terjual ?? 0 }}</td>
                                                <td class="fw-semibold">
                                                    {{ ($rekap->operasional->kloters->sum('jumlah_donat') ?? 0) - ($rekap->total_donat_terjual ?? 0) }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Daftar Transaksi -->
                        <div class="rekap-card-modal mb-4">
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
                        <div class="rekap-card-modal mb-4">
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
                                                    {{ $rekap->operasional->transaksis->count() }}
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
                        <div class="rekap-card-modal mb-4">
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
                        <div class="rekap-card-modal mb-4">
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
                        <div class="rekap-card-modal mb-2">
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
                        <div class="total-highlight"
                            style="background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%); color: #0c5460;">
                            <div class="total-amount">
                                Rp {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}
                            </div>
                            <div class="total-label">Total Pendapatan Hari Ini</div>
                        </div>
                        <div class="total-highlight"
                            style="background: linear-gradient(135deg,rgb(126, 126, 126) 0%,rgb(126, 126, 126) 100%); color:rgb(255, 255, 255);">
                            @php $cashHighlight = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0); @endphp
                            <div class="total-amount">
                                Rp {{ number_format($cashHighlight, 0, ',', '.') }}
                            </div>
                            <div class="total-label">Cash Di Pegawai</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        @if($rekap->status === 'pending' && (session('user') && session('user')->id === $rekap->pegawai_id))
                            <form action="{{ route('pegawai.rekap.delete', [$outlet->id, $rekap->id]) }}" method="POST"
                                onsubmit="return confirm('Hapus rekap ini? Tindakan tidak dapat dibatalkan.')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="bi bi-trash3 me-1"></i>Hapus Rekap
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection