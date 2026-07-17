@extends('layouts.app')

@section('title', 'Pengajuan Kasbon')

@section('css')
<style>
    .page-header {
        background: var(--primary-gradient);
        color: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        margin-bottom: 2rem;
        text-align: center;
        box-shadow: var(--box-shadow);
    }
    
    .page-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .kasbon-form-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 2rem;
        border: none;
        transition: all 0.3s ease;
    }
    
    .kasbon-form-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .form-section-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .form-section-title i {
        color: var(--primary-color);
        font-size: 1.5rem;
    }
    
    .form-floating {
        margin-bottom: 1rem;
    }
    
    .form-floating > .form-control {
        padding: 1.25rem 1rem;
        font-size: 1rem;
        border: 2px solid #e1e5e9;
        border-radius: var(--border-radius-sm);
        transition: all 0.3s ease;
        background: #f8f9fa;
    }
    
    .form-floating > .form-control:focus {
        background: white;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        transform: translateY(-1px);
    }
    
    .form-floating > label {
        padding: 1.25rem 1rem;
        font-weight: 500;
        color: var(--secondary-color);
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        padding: 1rem 2rem;
        font-size: 1.1rem;
        font-weight: 600;
        border-radius: var(--border-radius-sm);
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .submit-btn:hover {
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    }
    
    .history-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        border: none;
        transition: all 0.3s ease;
    }
    
    .history-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .status-badge {
        font-size: 0.8rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .table-responsive {
        border-radius: var(--border-radius-sm);
        overflow-x: auto;
        overflow-y: visible;
        box-shadow: var(--box-shadow-sm);
        -webkit-overflow-scrolling: touch;
    }
    
    .table {
        margin: 0;
        background: white;
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
    
    .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0,0,0,0.02);
    }
    
    .btn-action {
        padding: 0.375rem 0.75rem;
        font-size: 0.8rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: var(--box-shadow-sm);
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        color: white;
    }
    
    .btn-delete:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        color: white;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: var(--border-radius);
    }
    
    .empty-state i {
        font-size: 4rem;
        color: var(--secondary-color);
        margin-bottom: 1rem;
    }
    
    .empty-state h6 {
        color: var(--secondary-color);
        font-weight: 500;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: var(--secondary-color);
        margin: 0;
        font-size: 0.9rem;
    }
    
    .nominal-display {
        font-weight: 600;
        color: var(--success-color);
    }
    
    .date-display {
        font-weight: 600;
        color: var(--dark-color);
    }
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.25rem;
        }
        
        .kasbon-form-card .card-body {
            padding: 1rem;
        }
        
        .form-section-title {
            font-size: 1.1rem;
        }
        
        .submit-btn {
            width: 100%;
            justify-content: center;
            padding: 0.875rem 1.5rem;
            font-size: 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }
        
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 8px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
    }
    
    @media (max-width: 576px) {
        .page-header {
            padding: 0.75rem;
        }
        
        .kasbon-form-card .card-body {
            padding: 0.75rem;
        }
        
        .form-floating > .form-control {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .form-floating > label {
            padding: 1rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .table-responsive {
            font-size: 0.8rem;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e0 #f7fafc;
        }
        
        .table-responsive::-webkit-scrollbar {
            height: 6px;
        }
        
        .table-responsive::-webkit-scrollbar-track {
            background: #f7fafc;
            border-radius: 3px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 3px;
        }
        
        .table-responsive::-webkit-scrollbar-thumb:hover {
            background: #a0aec0;
        }
        
        .table th, .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
            white-space: nowrap;
        }
        
        .table th:first-child,
        .table td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 1;
        }
        
        .btn-action {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .status-badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        .empty-state {
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            font-size: 3rem;
        }
    }

    /* Pagination Styles */
    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        color: #495057;
        border-color: #dee2e6;
    }

    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }

    .pagination .page-link:hover {
        color: #0d6efd;
        background-color: #e9ecef;
    }
</style>
@endsection

@section('back-button')
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-wallet2"></i>
            Pengajuan Kasbon
        </h1>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Form Pengajuan Kasbon -->
    <div class="kasbon-form-card">
        <div class="card-body">
            <h5 class="form-section-title">
                <i class="bi bi-plus-circle"></i>
                Ajukan Kasbon Baru
            </h5>
            <div class="alert alert-info mb-3" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Informasi:</strong> Anda hanya dapat mengajukan kasbon sekali dalam sehari. Jika sudah mengajukan kasbon hari ini, silakan tunggu hingga besok.
            </div>
            <form id="form-kasbon" method="POST" action="{{ route('pegawai.kasbon.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <input type="number" class="form-control" id="nominal" name="nominal" min="1" placeholder="0" required>
                            <label for="nominal">
                                <i class="bi bi-cash me-2"></i>Nominal (Rp)
                            </label>
                        </div>
                    </div>
                    <div class="col-12 col-md-6">
                        <div class="form-floating">
                            <textarea class="form-control" id="keterangan" name="keterangan" style="height: 58px;" placeholder="Keterangan" required></textarea>
                            <label for="keterangan">
                                <i class="bi bi-chat-text me-2"></i>Keterangan
                            </label>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <button type="submit" class="submit-btn">
                        <i class="bi bi-send-fill"></i>
                        <span>Ajukan Kasbon</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Histori Pengajuan Kasbon -->
    <div class="history-card">
        <div class="card-body">
            <h5 class="form-section-title">
                <i class="bi bi-clock-history" style="color: #17a2b8;"></i>
                Histori Pengajuan Kasbon
            </h5>

            @if($kasbonHistori->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Pembayaran</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kasbonHistori as $kasbon)
                            <tr>
                                <td class="date-display">{{ \Carbon\Carbon::parse($kasbon->tanggal)->format('d/m/Y') }}</td>
                                <td class="nominal-display">Rp {{ number_format($kasbon->nominal, 0, ',', '.') }}</td>
                                <td>{{ Str::limit($kasbon->keterangan, 30) }}</td>
                                <td>
                                    @if($kasbon->status == 'pending')
                                        <span class="badge bg-warning status-badge">
                                            <i class="bi bi-clock"></i>Pending
                                        </span>
                                    @elseif($kasbon->status == 'approved')
                                        <span class="badge bg-success status-badge">
                                            <i class="bi bi-check-circle"></i>Disetujui
                                        </span>
                                    @elseif($kasbon->status == 'rejected')
                                        <span class="badge bg-danger status-badge">
                                            <i class="bi bi-x-circle"></i>Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($kasbon->status_pembayaran == 'lunas')
                                        <span class="badge bg-success status-badge">
                                            <i class="bi bi-check-circle-fill"></i>Lunas
                                        </span>
                                    @else
                                        <span class="badge bg-warning status-badge">
                                            <i class="bi bi-exclamation-triangle"></i>Belum Dibayar
                                        </span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $kasbon->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($kasbon->status == 'pending')
                                        <form action="{{ route('pegawai.kasbon.destroy', $kasbon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengajuan kasbon ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-action btn-delete">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if ($kasbonHistori->hasPages())
                    <nav aria-label="Page navigation" class="mt-3">
                        <ul class="pagination justify-content-center">
                            {{-- Previous Page Link --}}
                            @if ($kasbonHistori->onFirstPage())
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $kasbonHistori->previousPageUrl() }}" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $kasbonHistori->currentPage();
                                $lastPage = $kasbonHistori->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                            @endphp

                            {{-- First Page --}}
                            @if ($startPage > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $kasbonHistori->url(1) }}">1</a>
                                </li>
                                @if ($startPage > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            {{-- Page Numbers --}}
                            @for ($page = $startPage; $page <= $endPage; $page++)
                                @if ($page == $currentPage)
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $kasbonHistori->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Last Page --}}
                            @if ($endPage < $lastPage)
                                @if ($endPage < $lastPage - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $kasbonHistori->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($kasbonHistori->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $kasbonHistori->nextPageUrl() }}" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled">
                                    <span class="page-link" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                    
                    {{-- Info Pagination --}}
                    <div class="text-center text-muted mt-2">
                        <small>
                            Menampilkan {{ $kasbonHistori->firstItem() ?? 0 }} sampai {{ $kasbonHistori->lastItem() ?? 0 }} 
                            dari {{ $kasbonHistori->total() }} pengajuan kasbon
                        </small>
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <i class="bi bi-wallet"></i>
                    <h6>Belum Ada Pengajuan Kasbon</h6>
                    <p>Pengajuan kasbon Anda akan muncul di sini setelah diajukan.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
