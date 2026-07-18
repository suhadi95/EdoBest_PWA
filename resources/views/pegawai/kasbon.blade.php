@extends('layouts.app')

@section('title', 'Pengajuan Kasbon')

@section('back-button')
<a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Pengajuan Kasbon</h1>
            <p>Ajukan kasbon dan pantau status pengajuan</p>
        </div>
    </header>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <section class="ui-section">
        <h2 class="ui-section__title">Ajukan Kasbon Baru</h2>
        <div class="ui-panel">
            <div class="alert alert-info mb-3" role="alert">
                <i class="bi bi-info-circle me-2"></i>
                Anda hanya dapat mengajukan kasbon sekali dalam sehari. Jika sudah mengajukan hari ini, tunggu hingga besok.
            </div>
            <form id="form-kasbon" method="POST" action="{{ route('pegawai.kasbon.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-12 col-md-6">
                        <label for="nominal" class="form-label">Nominal (Rp)</label>
                        <input type="number" class="form-control" id="nominal" name="nominal" min="1" placeholder="0" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="1" placeholder="Keterangan" required></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-fill me-1"></i>Ajukan Kasbon
                    </button>
                </div>
            </form>
        </div>
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Histori Pengajuan</h2>

        @if($kasbonHistori->count() > 0)
            <div class="ui-panel" style="padding:0;overflow:hidden;">
                <div class="table-responsive" style="border:none;border-radius:0;">
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
                                <td class="fw-semibold">{{ \Carbon\Carbon::parse($kasbon->tanggal)->format('d/m/Y') }}</td>
                                <td class="fw-semibold" style="color:var(--success-color);">Rp {{ number_format($kasbon->nominal, 0, ',', '.') }}</td>
                                <td>{{ Str::limit($kasbon->keterangan, 30) }}</td>
                                <td>
                                    @if($kasbon->status == 'pending')
                                        <span class="ui-chip ui-chip--amber"><i class="bi bi-clock"></i>Pending</span>
                                    @elseif($kasbon->status == 'approved')
                                        <span class="ui-chip ui-chip--green"><i class="bi bi-check-circle"></i>Disetujui</span>
                                    @elseif($kasbon->status == 'rejected')
                                        <span class="ui-chip ui-chip--rose"><i class="bi bi-x-circle"></i>Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($kasbon->status_pembayaran == 'lunas')
                                        <span class="ui-chip ui-chip--green"><i class="bi bi-check-circle-fill"></i>Lunas</span>
                                    @else
                                        <span class="ui-chip ui-chip--amber"><i class="bi bi-exclamation-triangle"></i>Belum Dibayar</span>
                                    @endif
                                </td>
                                <td class="text-muted">{{ $kasbon->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($kasbon->status == 'pending')
                                        <form action="{{ route('pegawai.kasbon.destroy', $kasbon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus pengajuan kasbon ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
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
            </div>

            @if ($kasbonHistori->hasPages())
                <nav aria-label="Page navigation" class="mt-3">
                    <ul class="pagination justify-content-center">
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

                        @php
                            $currentPage = $kasbonHistori->currentPage();
                            $lastPage = $kasbonHistori->lastPage();
                            $startPage = max(1, $currentPage - 2);
                            $endPage = min($lastPage, $currentPage + 2);
                        @endphp

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

                <div class="text-center text-muted mt-2">
                    <small>
                        Menampilkan {{ $kasbonHistori->firstItem() ?? 0 }} sampai {{ $kasbonHistori->lastItem() ?? 0 }}
                        dari {{ $kasbonHistori->total() }} pengajuan kasbon
                    </small>
                </div>
            @endif
        @else
            <div class="ui-panel">
                <div class="ui-empty">
                    <i class="bi bi-wallet"></i>
                    <p class="mb-0">Belum ada pengajuan kasbon. Pengajuan Anda akan muncul di sini.</p>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection
