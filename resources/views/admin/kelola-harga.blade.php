@extends('layouts.app')

@section('title', 'Kelola Harga Item')

@section('css')
<style>
    .harga-card {
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin-bottom: 1rem;
        transition: transform 0.2s;
    }

    .harga-card:hover {
        transform: translateY(-2px);
    }

    .harga-card .card-body {
        padding: 1.25rem;
    }

    .harga-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .harga-title {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .harga-subtitle {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .price-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .price-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        border: 1px solid #dee2e6;
    }

    .price-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .price-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #198754;
        margin: 0;
    }

    .form-floating>label {
        padding: 1rem 0.75rem;
    }

    .form-floating>.form-control {
        padding: 1rem 0.75rem;
    }

    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }

        .harga-header {
            padding: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .harga-title {
            font-size: 1.2rem;
        }

        .harga-card .card-body {
            padding: 1.25rem;
        }

        .price-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.75rem;
        }

        .price-item {
            padding: 0.875rem;
        }

        .price-value {
            font-size: 1.2rem;
        }

        .btn-lg {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }

        .harga-card .card-body {
            padding: 1rem;
        }

        .harga-header {
            padding: 1rem;
            margin-bottom: 1rem;
        }

        .harga-title {
            font-size: 1.1rem;
            flex-direction: column;
            gap: 0.25rem;
        }

        .harga-subtitle {
            font-size: 0.85rem;
        }

        .harga-card .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .harga-card .d-flex .flex-grow-1 {
            order: 1;
            width: 100%;
        }

        .harga-card .btn {
            order: 2;
            width: 100%;
            margin-left: 0 !important;
            margin-top: 0.5rem;
        }

        .harga-card .card-body {
            padding: 1rem;
        }

        .harga-card .card-title {
            margin-bottom: 1rem;
        }

        .harga-card .price-grid {
            margin-bottom: 1rem;
        }

        .price-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .price-item {
            padding: 0.75rem 0.5rem;
        }

        .price-label {
            font-size: 0.75rem;
            margin-bottom: 0.375rem;
        }

        .price-value {
            font-size: 1.1rem;
        }

        .btn-lg {
            padding: 0.75rem;
            font-size: 0.85rem;
            min-height: 44px;
        }

        .btn-lg i {
            margin-right: 0.5rem;
        }

        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }

        .modal-body .form-control {
            font-size: 0.9rem;
        }

        .modal-body .form-label {
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .harga-header {
            padding: 0.75rem;
        }

        .harga-title {
            font-size: 1rem;
        }

        .harga-subtitle {
            font-size: 0.8rem;
        }

        .harga-card .card-body {
            padding: 0.75rem;
        }

        .harga-card .d-flex {
            flex-direction: column;
            gap: 0.75rem;
        }

        .harga-card .card-title {
            margin-bottom: 0.75rem;
        }

        .harga-card .price-grid {
            margin-bottom: 0.75rem;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.375rem;
        }

        .harga-card .btn {
            order: 2;
            width: 100%;
            margin-left: 0 !important;
            margin-top: 0;
            padding: 0.75rem;
            font-size: 0.85rem;
        }

        .price-item {
            padding: 0.5rem 0.25rem;
        }

        .price-label {
            font-size: 0.65rem;
            margin-bottom: 0.25rem;
        }

        .price-value {
            font-size: 0.9rem;
        }

        .btn-lg {
            padding: 0.625rem;
            font-size: 0.8rem;
        }

        .text-center.py-5 {
            padding: 2rem 1rem !important;
        }

        .text-center.py-5 i {
            font-size: 2rem !important;
        }

        .text-center.py-5 h6 {
            font-size: 0.9rem;
        }

        .text-center.py-5 p {
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

    <!-- Header -->
    <div class="harga-header">
        <div class="harga-title">
            <i class="bi bi-currency-dollar me-2"></i>Kelola Harga Item
        </div>
        <div class="harga-subtitle">
            Atur harga untuk mika, dus, dan box
        </div>
    </div>

    <!-- Daftar Harga Item -->
    @forelse ($hargaItems as $item)
    <div class="harga-card card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-box me-2"></i>{{ ucfirst($item->nama_item) }}
                    </h6>
                    <div class="price-grid">
                        <div class="price-item">
                            <div class="price-label">Reguler</div>
                            <div class="price-value">Rp {{ number_format($item->harga_reguler, 0, ',', '.') }}</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Classic</div>
                            <div class="price-value">Rp {{ number_format($item->harga_classic, 0, ',', '.') }}</div>
                        </div>
                        <div class="price-item">
                            <div class="price-label">Custom</div>
                            <div class="price-value">Rp {{ number_format($item->harga_costum, 0, ',', '.') }}</div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary btn-lg ms-3" onclick="editHarga({{ $item->id }})">
                    <i class="bi bi-pencil-square me-2"></i>Edit Harga
                </button>
            </div>
        </div>
    </div>
    @empty
    <div class="text-center py-5">
        <i class="bi bi-currency-dollar text-muted" style="font-size: 3rem;"></i>
        <h6 class="text-muted mt-3">Belum ada data harga</h6>
        <p class="text-muted">Data harga item akan muncul di sini.</p>
    </div>
    @endforelse
</div>

<!-- Modal Edit Harga -->
<div class="modal fade" id="hargaModal" tabindex="-1" aria-labelledby="hargaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hargaModalLabel">Edit Harga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="hargaForm">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="method">
                <input type="hidden" id="harga_id" name="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_item" class="form-label">Nama Item</label>
                        <input type="text" class="form-control" id="nama_item" disabled>
                    </div>
                    <div class="mb-3">
                        <label for="harga_reguler" class="form-label">Harga Reguler (Rp)</label>
                        <input type="number" class="form-control" id="harga_reguler" name="harga_reguler" min="0" step="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_classic" class="form-label">Harga Classic (Rp)</label>
                        <input type="number" class="form-control" id="harga_classic" name="harga_classic" min="0" step="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_costum" class="form-label">Harga Custom (Rp)</label>
                        <input type="number" class="form-control" id="harga_costum" name="harga_costum" min="0" step="1" required>
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

@section('js')
<script>
    function editHarga(id) {
        fetch(`/admin/kelola-harga/${id}/edit`, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal mengambil data harga');
                }
                return response.json();
            })
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                document.getElementById('harga_id').value = data.id;
                document.getElementById('nama_item').value = data.nama_item;
                document.getElementById('harga_reguler').value = data.harga_reguler;
                document.getElementById('harga_classic').value = data.harga_classic;
                document.getElementById('hargaModalLabel').textContent = 'Edit Harga ' + data.nama_item;
                new bootstrap.Modal(document.getElementById('hargaModal')).show();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal mengambil data harga: ' + error.message);
            });
    }

    document.getElementById('hargaForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const id = document.getElementById('harga_id').value;

        fetch(`/admin/kelola-harga/${id}`, {
                method: 'POST', // Spoofing PUT
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
                    bootstrap.Modal.getInstance(document.getElementById('hargaModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Gagal menyimpan data'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'Gagal menyimpan data';
                try {
                    const parsedError = JSON.parse(error.message);
                    errorMessage = Object.values(parsedError).flat().join(', ');
                } catch (e) {
                    errorMessage = error.message;
                }
                alert('Gagal menyimpan data: ' + errorMessage);
            });
    });
</script>
@endsection