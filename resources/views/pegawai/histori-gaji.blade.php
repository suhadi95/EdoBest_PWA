@extends('layouts.app')

@section('title', 'Histori Gaji')

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
    
    .back-btn {
        position: absolute;
        left: 1.5rem;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255,255,255,0.2);
        color: white;
        border: 1px solid rgba(255,255,255,0.3);
        padding: 0.5rem 1rem;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .back-btn:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        border-color: rgba(255,255,255,0.5);
    }
    
    .gaji-summary {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 1rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .gaji-summary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .summary-card {
        padding: 1.5rem;
        text-align: center;
        position: relative;
    }
    
    .summary-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--primary-gradient);
    }
    
    .summary-title {
        font-size: 0.9rem;
        color: var(--secondary-color);
        margin-bottom: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .summary-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--dark-color);
        margin: 0;
    }
    
    .summary-value.success {
        color: var(--success-color);
    }
    
    .summary-value.primary {
        color: var(--primary-color);
    }
    
    .gaji-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 1rem;
        border: none;
        transition: all 0.3s ease;
    }
    
    .gaji-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .status-paid {
        color: var(--success-color);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .status-unpaid {
        color: var(--danger-color);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .gaji-amount {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--success-color);
    }
    
    .table-responsive {
        border-radius: var(--border-radius-sm);
        overflow: hidden;
        box-shadow: var(--box-shadow-sm);
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
    
    .btn-detail {
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        border-radius: 6px;
        transition: all 0.3s ease;
        border: none;
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-detail:hover {
        background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
        color: white;
        transform: translateY(-1px);
        box-shadow: var(--box-shadow-sm);
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
    
    .modal {
        z-index: 1055 !important;
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

        .page-header {
            padding: 1rem;
            margin-bottom: 1.5rem;
            position: relative;
        }
        
        .page-title {
            font-size: 1.25rem;
        }
        
        .back-btn {
            position: static;
            transform: none;
            margin-bottom: 1rem;
            align-self: flex-start;
        }
        
        .summary-card {
            padding: 1rem;
        }
        
        .summary-value {
            font-size: 1.5rem;
        }
        
        .gaji-card .card-body {
            padding: 1rem;
        }
        
        .table th, .table td {
            padding: 0.75rem 0.5rem;
            font-size: 0.85rem;
        }
        
        .btn-detail {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
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

        .page-header {
            padding: 0.75rem;
        }
        
        .summary-card {
            padding: 0.75rem;
        }
        
        .summary-value {
            font-size: 1.25rem;
        }
        
        .gaji-card .card-body {
            padding: 0.75rem;
        }
        
        .table-responsive {
            font-size: 0.8rem;
        }
        
        .table th, .table td {
            padding: 0.5rem 0.25rem;
            font-size: 0.8rem;
        }
        
        .btn-detail {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .empty-state {
            padding: 3rem 1rem;
        }
        
        .empty-state i {
            font-size: 3rem;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Handle klik tombol Detail Gaji
    $(document).on('click', '.btn-detail-gaji', function() {
        const gajiId = $(this).data('gaji-id');
        const modal = $('#detailGajiModal');
        
        // Reset modal content
        modal.find('.loading-indicator').show();
        modal.find('.detail-content').hide().empty();
        
        // Show modal
        modal.modal('show');

        // Load detail gaji via AJAX
        $.ajax({
            url: '/pegawai/histori-gaji/detail/' + gajiId,
            type: 'GET',
            success: function(data) {
                // Hide loading indicator
                modal.find('.loading-indicator').hide();
                
                // Show content
                modal.find('.detail-content').html(data.html).show();
            },
            error: function(xhr) {
                modal.find('.loading-indicator').hide();
                modal.find('.detail-content').html('<div class="alert alert-danger">Terjadi kesalahan saat memuat detail gaji</div>').show();
            }
        });
    });
});
</script>
@endsection

@section('back-button')
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="container-fluid px-2 px-md-3">
    <!-- Page Header -->
    <div class="page-header position-relative">
        <h1 class="page-title">
            <i class="bi bi-cash-stack"></i>
            Histori Gaji
        </h1>
    </div>

    <!-- Alert Messages -->
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



    <!-- Histori Gaji Table -->
    <div class="gaji-card">
        <div class="card-body">
            <h6 class="card-title mb-3" style="font-size: 1.25rem; font-weight: 600; color: var(--dark-color); display: flex; align-items: center; gap: 0.75rem;">
                <i class="bi bi-list-ul" style="color: var(--primary-color); font-size: 1.5rem;"></i>
                Daftar Histori Gaji
            </h6>

            @if($gajiHistori->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Tanggal Gaji</th>
                                <th>Gaji Total</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gajiHistori as $gaji)
                                <tr>
                                    <td>
                                        @if($gaji->periode_mulai && $gaji->periode_akhir)
                                            {{ \Carbon\Carbon::parse($gaji->periode_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($gaji->periode_akhir)->format('d/m/Y') }}
                                            @if($gaji->periode_keterangan)
                                                <br><small class="text-muted">{{ $gaji->periode_keterangan }}</small>
                                            @endif
                                        @else
                                            {{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d F Y') }}
                                        @endif
                                    </td>
                                    <td class="fw-semibold">{{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d F Y') }}</td>
                                    <td>
                                        <span class="gaji-amount">Rp {{ number_format($gaji->gaji_total, 0, ',', '.') }}</span>
                                    </td>
                                    <td>
                                        @if($gaji->status === 'paid')
                                            <span class="status-paid">
                                                <i class="bi bi-check-circle-fill"></i>
                                                <span>Dibayar</span>
                                            </span>
                                        @else
                                            <span class="status-unpaid">
                                                <i class="bi bi-exclamation-triangle-fill"></i>
                                                <span>Belum Dibayar</span>
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-muted">{{ $gaji->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <button class="btn-detail btn-detail-gaji" data-gaji-id="{{ $gaji->id }}">
                                            <i class="bi bi-eye"></i>
                                            <span>Detail</span>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="bi bi-inbox text-muted" style="font-size: 2rem;"></i>
                                        <p class="text-muted mt-2 mb-0">Belum ada histori gaji.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($gajiHistori->hasPages())
                <div class="pagination-wrapper">
                    <nav aria-label="Navigasi halaman histori gaji">
                        <ul class="pagination">
                            <!-- Previous Page Link -->
                            @if ($gajiHistori->onFirstPage())
                                <li class="page-item disabled">
                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $gajiHistori->previousPageUrl() }}" rel="prev">Previous</a>
                                </li>
                            @endif

                            <!-- Pagination Elements -->
                            @foreach ($gajiHistori->getUrlRange(1, $gajiHistori->lastPage()) as $page => $url)
                                @if ($page == $gajiHistori->currentPage())
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
                            @if ($gajiHistori->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $gajiHistori->nextPageUrl() }}" rel="next">Next</a>
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
                <div class="empty-state">
                    <i class="bi bi-cash-stack"></i>
                    <h6>Belum Ada Histori Gaji</h6>
                    <p>Histori gaji Anda akan muncul di sini setelah periode gaji selesai.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

<div class="modal fade" id="detailGajiModal" tabindex="-1" aria-labelledby="detailGajiModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailGajiModalLabel">Detail Gaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Konten modal akan dimuat via AJAX -->
                <div class="text-center loading-indicator">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Memuat detail gaji...</p>
                </div>
                <div class="detail-content" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
