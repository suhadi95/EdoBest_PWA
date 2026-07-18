@extends('layouts.app')

@section('title', 'Histori Gaji')

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
<a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Histori Gaji</h1>
            <p>Daftar gaji yang sudah dicatat</p>
        </div>
    </header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Histori Gaji</h2>

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
                                    <span class="fw-semibold" style="color:var(--success-color);">Rp {{ number_format($gaji->gaji_total, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @if($gaji->status === 'paid')
                                        <span class="ui-chip ui-chip--green"><i class="bi bi-check-circle-fill"></i>Dibayar</span>
                                    @else
                                        <span class="ui-chip ui-chip--rose"><i class="bi bi-exclamation-triangle-fill"></i>Belum Dibayar</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $gaji->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-detail-gaji" data-gaji-id="{{ $gaji->id }}">
                                        <i class="bi bi-eye me-1"></i>Detail
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

            @if ($gajiHistori->hasPages())
                <nav aria-label="Navigasi halaman histori gaji" class="mt-3">
                    <ul class="pagination justify-content-center">
                        @if ($gajiHistori->onFirstPage())
                            <li class="page-item disabled">
                                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $gajiHistori->previousPageUrl() }}" rel="prev">Previous</a>
                            </li>
                        @endif

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
            @endif
        @else
            <div class="ui-panel">
                <div class="ui-empty">
                    <i class="bi bi-cash-stack"></i>
                    <p class="mb-0">Belum ada histori gaji. Histori akan muncul setelah periode gaji selesai.</p>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

@section('modals')
<div class="modal fade" id="detailGajiModal" tabindex="-1" aria-labelledby="detailGajiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailGajiModalLabel">Detail Gaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
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
@endsection
