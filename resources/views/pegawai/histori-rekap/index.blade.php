@extends('layouts.app')

@section('title', 'Histori Rekap')

@section('css')
<style>
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
        color: var(--muted);
    }

    .rekap-card-modal {
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

    .total-highlight {
        background: #eef6ff;
        color: #2563eb;
        padding: 1rem;
        border-radius: var(--border-radius-sm);
        text-align: center;
        margin: 1rem 0;
        border: 1px solid #dbeafe;
    }

    .total-highlight--muted {
        background: #f1f5f9;
        color: #475569;
        border-color: var(--border);
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
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        .summary-grid {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .table-responsive {
            font-size: 0.8rem;
        }

        .table th,
        .table td {
            padding: 0.4rem 0.2rem;
            white-space: nowrap;
        }
    }

    @media (max-width: 576px) {
        .modal-dialog {
            margin: 0.25rem;
            max-width: calc(100% - 0.5rem);
        }

        .summary-value { font-size: 1rem; }
        .summary-label { font-size: 0.7rem; }
        .payment-method-badge {
            font-size: 0.65rem;
            padding: 0.1em 0.25em;
        }
    }
</style>
@endsection

@section('back-button')
<a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
@php
    $totalRekap = $historiRekap->total();
    $validatedCount = $historiRekap->where('status', 'validated')->count();
    $pendingCount = $historiRekap->where('status', '!=', 'validated')->count();
    $totalPendapatanPage = $historiRekap->sum(fn ($r) => $r->total_uang ?? 0);
@endphp

<div class="ui-page ui-page--wide">
    <header class="ui-header">
        <div>
            <h1>Histori Rekap</h1>
            <p>Rekap harian yang sudah Anda buat</p>
        </div>
        <div class="ui-header__meta">
            <strong>{{ $outlet->nama }}</strong>
            {{ $outlet->alamat }}
        </div>
    </header>

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

    <div class="ui-stats">
        <div class="ui-stat">
            <span>Total Rekap</span>
            <strong>{{ $totalRekap }}</strong>
        </div>
        <div class="ui-stat">
            <span>Divalidasi</span>
            <strong>{{ $validatedCount }}</strong>
        </div>
        <div class="ui-stat">
            <span>Menunggu</span>
            <strong>{{ $pendingCount }}</strong>
        </div>
        <div class="ui-stat">
            <span>Pendapatan (halaman)</span>
            <strong>Rp {{ number_format($totalPendapatanPage, 0, ',', '.') }}</strong>
        </div>
    </div>

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Histori Rekap</h2>

        @if($historiRekap->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
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
                                    <span class="fw-semibold" style="color:var(--success-color);">Rp
                                        {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}</span>
                                    @php
                                        $cashList = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0);
                                    @endphp
                                    <div class="small text-muted">Cash: Rp {{ number_format($cashList, 0, ',', '.') }}</div>
                                </td>
                                <td>
                                    <span class="ui-chip ui-chip--sky">{{ $rekap->operasional->transaksis->count() ?? 0 }} transaksi</span>
                                </td>
                                <td>
                                    @if($rekap->status === 'validated')
                                        <span class="ui-chip ui-chip--green"><i class="bi bi-check-circle-fill"></i>Divalidasi</span>
                                    @else
                                        <span class="ui-chip ui-chip--amber"><i class="bi bi-clock"></i>Menunggu Validasi</span>
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
                                        <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal"
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
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
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

            @if ($historiRekap->hasPages())
                <nav aria-label="Navigasi halaman histori rekap" class="mt-3">
                    <ul class="pagination justify-content-center">
                        @if ($historiRekap->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $historiRekap->previousPageUrl() }}" rel="prev">Previous</a>
                            </li>
                        @endif

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
            @endif
        @else
            <div class="ui-panel">
                <div class="ui-empty">
                    <i class="bi bi-file-earmark-check"></i>
                    <p class="mb-0">Belum ada histori rekap. Histori akan muncul setelah membuat rekap harian.</p>
                </div>
            </div>
        @endif
    </section>
</div>

@foreach ($historiRekap as $rekap)
    <div class="modal fade" id="rekapModal{{ $rekap->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Rekap Harian - {{ $outlet->nama }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold">Tanggal:</span>
                            <span>{{ \Carbon\Carbon::parse($rekap->tanggal)->format('d-m-Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="fw-semibold">Status Validasi:</span>
                            @if($rekap->status === 'validated')
                                <span class="ui-chip ui-chip--green"><i class="bi bi-check-circle-fill"></i>Divalidasi</span>
                            @else
                                <span class="ui-chip ui-chip--amber"><i class="bi bi-clock"></i>Menunggu Validasi</span>
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
                        <div class="summary-card" style="background:var(--success-color);border-color:var(--success-color);">
                            <div class="summary-value" style="color: white;">Rp
                                {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}</div>
                            <div class="summary-label" style="color: white;">Total Pendapatan Hari Ini</div>
                        </div>
                        <div class="summary-card" style="background:#64748b;border-color:#64748b;">
                            @php $cashModal = $rekap->cash_di_pegawai ?? ($rekap->total_tunai ?? 0); @endphp
                            <div class="summary-value" style="color: white;">Rp {{ number_format($cashModal, 0, ',', '.') }}
                            </div>
                            <div class="summary-label" style="color: white;">Cash Di Pegawai</div>
                        </div>
                    </div>

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

                    <div class="rekap-card-modal mb-4">
                        <div class="card-body p-2">
                            <h6 class="section-title mb-2">
                                <i class="bi bi-receipt"></i>
                                Daftar Transaksi
                            </h6>
                            @if ($rekap->operasional && $rekap->operasional->transaksis && $rekap->operasional->transaksis->count() > 0)
                                @php
                                    $sortedTransaksis = $rekap->operasional->transaksis->sortByDesc('created_at')->values();

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
                                <div class="ui-empty py-4">
                                    <i class="bi bi-receipt"></i>
                                    <p class="mb-0">Belum ada transaksi.</p>
                                </div>
                            @endif
                        </div>
                    </div>

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

                    <div class="total-highlight">
                        <div class="total-amount">
                            Rp {{ number_format($rekap->total_uang ?? 0, 0, ',', '.') }}
                        </div>
                        <div class="total-label">Total Pendapatan Hari Ini</div>
                    </div>
                    <div class="total-highlight total-highlight--muted">
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
