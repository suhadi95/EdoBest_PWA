@extends('layouts.app')

@section('title', 'Kelola Harga Item')

@section('css')
<style>
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .btn-sm {
        font-size: 0.8rem;
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.9rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-currency-dollar me-2"></i>Kelola Harga Item</h5>
                <p>Atur harga untuk mika, dus, dan box (Original dan Klasik).</p>
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

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama Item</th>
                                <th>Harga Original</th>
                                <th>Harga Klasik</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($hargaItems as $item)
                                <tr>
                                    <td>{{ ucfirst($item->nama_item) }}</td>
                                    <td>Rp {{ number_format($item->harga_original, 0, ',', '.') }}</td>
                                    <td>Rp {{ number_format($item->harga_klasik, 0, ',', '.') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editHarga({{ $item->id }})">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data harga.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
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
                        <label for="harga_original" class="form-label">Harga Original (Rp)</label>
                        <input type="number" class="form-control" id="harga_original" name="harga_original" min="0" step="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="harga_klasik" class="form-label">Harga Klasik (Rp)</label>
                        <input type="number" class="form-control" id="harga_klasik" name="harga_klasik" min="0" step="1" required>
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
            document.getElementById('harga_original').value = data.harga_original;
            document.getElementById('harga_klasik').value = data.harga_klasik;
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
                return response.json().then(err => { throw new Error(JSON.stringify(err.error)) });
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