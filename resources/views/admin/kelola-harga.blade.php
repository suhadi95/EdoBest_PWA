@extends('layouts.app')

@section('title', 'Kelola Harga Item')

@section('content')
<div class="ui-page">
    <a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <header class="ui-header">
        <div>
            <h1>Kelola Harga</h1>
            <p>Atur harga untuk mika, dus, dan box</p>
        </div>
    </header>

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

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Harga Item</h2>
        @forelse ($hargaItems as $item)
        <div class="ui-panel">
            <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                <div style="flex:1;min-width:0;">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="ui-menu__icon ui-icon--slate"><i class="bi bi-box"></i></div>
                        <strong style="font-size:1rem;">{{ ucfirst($item->nama_item) }}</strong>
                    </div>
                    <div class="ui-stats" style="margin-bottom:0;">
                        <div class="ui-stat">
                            <span>Reguler</span>
                            <strong>Rp {{ number_format($item->harga_reguler, 0, ',', '.') }}</strong>
                        </div>
                        <div class="ui-stat">
                            <span>Classic</span>
                            <strong>Rp {{ number_format($item->harga_classic, 0, ',', '.') }}</strong>
                        </div>
                        <div class="ui-stat">
                            <span>Custom</span>
                            <strong>Rp {{ number_format($item->harga_costum, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="editHarga({{ $item->id }})">
                    <i class="bi bi-pencil-square me-1"></i>Edit
                </button>
            </div>
        </div>
        @empty
        <div class="ui-empty">
            <i class="bi bi-currency-dollar"></i>
            <p class="mb-0">Belum ada data harga. Data harga item akan muncul di sini.</p>
        </div>
        @endforelse
    </section>
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
