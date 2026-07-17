@extends('layouts.app')

@section('title', 'Detail Operasional - {{ $outlet->nama }}')

@section('css')
    <style>
        .card-body p {
            margin-bottom: 10px;
        }

        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        /* Modal Detail Rekap Styles */
        .rekap-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            border: 1px solid #e9ecef;
        }

        .rekap-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .section-title {
            font-size: 0.9rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.75rem;
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

        .payment-method-badge {
            font-size: 0.75rem;
            padding: 0.25em 0.5em;
        }

        .total-highlight {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
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

        .status-validated {
            color: #198754;
            font-weight: 600;
        }

        .table-hover.table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.02);
        }

        .table th {
            background: var(--light-color);
            border: none;
            font-weight: 600;
            color: var(--dark-color);
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }

        .table td {
            border: none;
            padding: 1rem 0.75rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }

        .payment-method-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
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

            .summary-card {
                padding: 1rem;
            }

            .summary-value {
                font-size: 1.5rem;
            }

            .rekap-card .card-body {
                padding: 1rem;
            }

            .table th,
            .table td {
                padding: 0.75rem 0.5rem;
                font-size: 0.85rem;
            }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #e9ecef;
        }

        .stat-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: #198754;
            margin-bottom: 0.25rem;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
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
            .table-responsive {
                font-size: 0.9rem;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
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
    </style>
@endsection

@section('back-button')
    <a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-3">
            <div class="col-12">
                <h4>{{ $outlet->nama }}</h4>
                <p class="text-muted">Alamat: {{ $outlet->alamat }}</p>
                <p class="text-muted">Tanggal: {{ \Carbon\Carbon::today()->format('d-m-Y') }}</p>
            </div>
        </div>

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

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        @if (!$operasional)
                            <!-- Operasional belum dimulai -->
                            <div class="alert alert-info">Operasional belum dimulai hari ini.</div>
                            <form action="{{ route('operasional.mulai') }}" method="POST">
                                @csrf
                                <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                                <button type="submit" class="btn btn-primary">Mulai Operasional</button>
                            </form>
                        @else
                            <!-- Operasional sudah aktif -->
                            <p>Status:
                                @php
                                    if ($rekap && $rekap->status === 'validated') {
                                        $statusText = 'Selesai';
                                        $badgeClass = 'primary';
                                    } else {
                                        $statusText = ucfirst($operasional->status);
                                        $badgeClass = $operasional->status === 'aktif' ? 'success' : 'danger';
                                    }
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $statusText }}</span>
                            </p>

                            <!-- Tabel Sisa Kemasan -->
                            <h6>Sisa Kemasan</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Jenis Kemasan</th>
                                            <th>Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Mika</td>
                                            <td>{{ $totalMika }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dus 1</td>
                                            <td>{{ $totalDus1 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dus 2</td>
                                            <td>{{ $totalDus2 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dus 3</td>
                                            <td>{{ $totalDus3 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Box</td>
                                            <td>{{ $totalBox }}</td>
                                        </tr>
                                        <tr>
                                            <td>Box 12</td>
                                            <td>{{ $totalBox12 }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lilin</td>
                                            <td>{{ $stokOutlet->stok_lilin ?? 0 }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Tambah Kloter -->
                            @if ($operasional->status === 'aktif' && !$rekap)
                                <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#kloterModal">
                                    <i class="bi bi-plus-circle me-2"></i>Tambah Kloter
                                </button>
                            @endif

                            <!-- Tabel Daftar Kloter -->
                            <h6 style="margin-top: 1em">Daftar Kloter</h6>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>No</th>
                                            <th>Jumlah Donat</th>
                                            <th>Mika</th>
                                            <th>Dus 1</th>
                                            <th>Dus 2</th>
                                            <th>Dus 3</th>
                                            <th>Box</th>
                                            <th>Box 12</th>
                                            <th>Lilin</th>
                                            <th>Waktu</th>
                                            <th>Aksi</th>
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
                                                <td>{{ $kloter->jumlah_lilin ?? 0 }}</td>
                                                <td>{{ $kloter->created_at->format('H:i:s') }}</td>
                                                <td>
                                                    @if (!$rekap)
                                                        <form action="{{ route('operasional.kloter.delete', $kloter->id) }}"
                                                            method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus kloter ini?')">
                                                                <i class="bi bi-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="11" class="text-center">Belum ada kloter hari ini.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Rekap Harian -->
                            @if ($rekap)
                                <h6>Rekap Harian</h6>
                                <p>Total Donat Terjual: {{ $rekap->total_donat_terjual }}</p>
                                <p>Total Uang Penjualan: Rp
                                    {{ number_format($rekap->total_uang_penjualan ?? $rekap->total_uang, 0, ',', '.') }}
                                </p>
                                <p>Total Pendapatan: Rp
                                    {{ number_format($rekap->total_uang ?? $rekap->total_uang, 0, ',', '.') }}
                                </p>
                                <button class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#rekapModal">
                                    <i class="bi bi-file-earmark-text me-2"></i>Lihat Rekap
                                </button>
                                @if ($rekap->status !== 'validated')
                                    <form action="{{ route('admin.rekap.validasi', [$outlet->id, $rekap->id]) }}" method="POST"
                                        style="display: inline; margin-left: 10px;">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm">
                                            <i class="bi bi-check-circle me-2"></i>Validasi Rekap
                                        </button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Kloter -->
        <div class="modal fade" id="kloterModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Tambah Kloter</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('operasional.kloter.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="operasional_id" value="{{ $operasional->id ?? '' }}">
                        <div class="modal-body">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <div class="mb-3">
                                <label for="jumlah_donat" class="form-label">Jumlah Donat</label>
                                <input type="number" class="form-control" name="jumlah_donat" min="0"
                                    value="{{ old('jumlah_donat') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_mika" class="form-label">Jumlah Mika</label>
                                <input type="number" class="form-control" name="jumlah_mika" min="0"
                                    value="{{ old('jumlah_mika') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_dus1" class="form-label">Jumlah Dus 1</label>
                                <input type="number" class="form-control" name="jumlah_dus1" min="0"
                                    value="{{ old('jumlah_dus1') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_dus2" class="form-label">Jumlah Dus 2</label>
                                <input type="number" class="form-control" name="jumlah_dus2" min="0"
                                    value="{{ old('jumlah_dus2') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_dus3" class="form-label">Jumlah Dus 3</label>
                                <input type="number" class="form-control" name="jumlah_dus3" min="0"
                                    value="{{ old('jumlah_dus3') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_box" class="form-label">Jumlah Box</label>
                                <input type="number" class="form-control" name="jumlah_box" min="0"
                                    value="{{ old('jumlah_box') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_box12" class="form-label">Jumlah Box 12</label>
                                <input type="number" class="form-control" name="jumlah_box12" min="0"
                                    value="{{ old('jumlah_box12') }}">
                            </div>
                            <div class="mb-3">
                                <label for="jumlah_lilin" class="form-label">Jumlah Lilin</label>
                                <input type="number" class="form-control" name="jumlah_lilin" min="0"
                                    value="{{ old('jumlah_lilin') }}">
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

        <!-- Modal Detail Rekap -->
        @if ($rekap)
            <div class="modal fade" id="rekapModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Rekap Harian - {{ $outlet->nama }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                                    @php
                                        $cashSummary = $rekap->cash_di_pegawai ?? ($cashPegawai ?? ($rekap->total_tunai ?? 0));
                                    @endphp
                                    <div class="summary-value" style="color: white;">Rp
                                        {{ number_format($cashSummary, 0, ',', '.') }}</div>
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
                                                            {{ number_format($sortedTransaksis->sum('total_harga'), 0, ',', '.') }}
                                                        </th>
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

                            <!-- Tabel Stok Kemasan -->
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
                                                    <th class="fw-bold {{ $netCatatan >= 0 ? 'text-success' : 'text-danger' }}">
                                                        Rp {{ number_format($netCatatan, 0, ',', '.') }}</th>
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
                                @php $cashHighlight = $rekap->cash_di_pegawai ?? ($cashPegawai ?? ($rekap->total_tunai ?? 0)); @endphp
                                <div class="total-amount">
                                    Rp {{ number_format($cashHighlight, 0, ',', '.') }}
                                </div>
                                <div class="total-label">Cash Di Pegawai</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        @forelse ($rekap->operasional->transaksis ?? [] as $transaksi)
            <div class="modal fade nested-modal" id="transaksiModal{{ $transaksi->id }}" tabindex="-1" aria-hidden="true"
                data-bs-backdrop="static" style="z-index: 1055;">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Detail Transaksi -
                                {{ \Carbon\Carbon::parse($transaksi->created_at)->format('d-m-Y H:i:s') }}
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Metode Pembayaran:</strong> {{ ucfirst($transaksi->metode_pembayaran) }}</p>
                            <p><strong>Total Donat:</strong> {{ $transaksi->total_donat }}</p>
                            <p><strong>Total Harga:</strong> Rp {{ number_format($transaksi->total_harga, 0, ',', '.') }}
                            </p>
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
                                    @if (count($transaksi->items) > 0)
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
@endsection