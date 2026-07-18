@extends('layouts.app')

@section('title', 'Detail Stok Kemasan - {{ $outlet->nama }}')

@section('css')
    <style>
        .table th,
        .table td {
            vertical-align: middle;
        }

        @media (max-width: 576px) {
            .table-responsive {
                font-size: 0.9rem;
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

            .table th,
            .table td {
                white-space: nowrap;
                min-width: 120px;
            }

            .table th:first-child,
            .table td:first-child {
                position: sticky;
                left: 0;
                background: white;
                z-index: 1;
            }

            .btn-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.5rem;
            }

            .modal-dialog {
                margin: 0.5rem;
            }
        }

        .pagination .page-link {
            color: var(--brand);
            border-color: var(--border);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }

        .pagination .page-link:hover {
            color: var(--brand-dark);
            background-color: #f3f4ff;
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
            <h1>Detail Stok Kemasan</h1>
            <p>{{ $outlet->nama }} · {{ $outlet->alamat }}</p>
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

    <section class="ui-section">
        <h2 class="ui-section__title">Sisa Stok Kemasan</h2>
        <div class="ui-panel" style="padding:0; overflow:hidden;">
            <div class="table-responsive" style="border:none; border-radius:0;">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Jenis Kemasan</th>
                            <th>Jumlah Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Mika</td>
                            <td>{{ $stokOutlet->stok_mika }}</td>
                        </tr>
                        <tr>
                            <td>Dus 1</td>
                            <td>{{ $stokOutlet->stok_dus1 }}</td>
                        </tr>
                        <tr>
                            <td>Dus 2</td>
                            <td>{{ $stokOutlet->stok_dus2 }}</td>
                        </tr>
                        <tr>
                            <td>Dus 3</td>
                            <td>{{ $stokOutlet->stok_dus3 }}</td>
                        </tr>
                        <tr>
                            <td>Box</td>
                            <td>{{ $stokOutlet->stok_box }}</td>
                        </tr>
                        <tr>
                            <td>Box 12</td>
                            <td>{{ $stokOutlet->stok_box12 }}</td>
                        </tr>
                        <tr>
                            <td>Lilin</td>
                            <td>{{ $stokOutlet->stok_lilin ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <button class="btn btn-outline-primary mb-0" data-bs-toggle="modal" data-bs-target="#stokModal">
            <i class="bi bi-pencil-square me-2"></i>Update Stok Manual
        </button>
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Histori Stok</h2>
        <div class="ui-panel" style="padding:0; overflow:hidden;">
            <div class="table-responsive" style="border:none; border-radius:0;">
                <table class="table table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Jenis Stok</th>
                            <th>Perubahan</th>
                            <th>Keterangan</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($historiStoks as $histori)
                            <tr>
                                <td>{{ $historiStoks->firstItem() + $loop->index }}</td>
                                <td>{{ ucfirst($histori->jenis_stok) }}</td>
                                <td>{{ $histori->jumlah_perubahan > 0 ? '+' : '' }}{{ $histori->jumlah_perubahan }}</td>
                                <td>{{ $histori->keterangan }}</td>
                                <td>{{ $histori->created_at->format('d-m-Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Belum ada histori stok.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if ($historiStoks->hasPages())
            <nav aria-label="Page navigation" class="mt-3">
                <ul class="pagination justify-content-center">
                    @if ($historiStoks->onFirstPage())
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $historiStoks->previousPageUrl() }}" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    @endif

                    @php
                        $currentPage = $historiStoks->currentPage();
                        $lastPage = $historiStoks->lastPage();
                        $startPage = max(1, $currentPage - 2);
                        $endPage = min($lastPage, $currentPage + 2);
                    @endphp

                    @if ($startPage > 1)
                        <li class="page-item">
                            <a class="page-link" href="{{ $historiStoks->url(1) }}">1</a>
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
                                <a class="page-link" href="{{ $historiStoks->url($page) }}">{{ $page }}</a>
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
                            <a class="page-link" href="{{ $historiStoks->url($lastPage) }}">{{ $lastPage }}</a>
                        </li>
                    @endif

                    @if ($historiStoks->hasMorePages())
                        <li class="page-item">
                            <a class="page-link" href="{{ $historiStoks->nextPageUrl() }}" aria-label="Next">
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
                    Menampilkan {{ $historiStoks->firstItem() ?? 0 }} sampai {{ $historiStoks->lastItem() ?? 0 }}
                    dari {{ $historiStoks->total() }} histori stok
                </small>
            </div>
        @endif
    </section>
</div>

    <!-- Modal Update Stok Manual -->
    <div class="modal fade" id="stokModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Stok Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('stok.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="jenis_stok" class="form-label">Jenis Stok</label>
                            <select class="form-select" name="jenis_stok" required>
                                <option value="mika">Mika</option>
                                <option value="dus1">Dus 1</option>
                                <option value="dus2">Dus 2</option>
                                <option value="dus3">Dus 3</option>
                                <option value="box">Box</option>
                                <option value="box12">Box 12</option>
                                <option value="lilin">Lilin</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="jumlah_perubahan" class="form-label">Jumlah Perubahan (positif untuk tambah, negatif
                                untuk kurang)</label>
                            <input type="number" class="form-control" name="jumlah_perubahan" required>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <input type="text" class="form-control" name="keterangan"
                                placeholder="Masukkan keterangan, misalnya: Koreksi stok" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
