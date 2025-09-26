@extends('layouts.app')

@section('title', 'Kelola Outlet')

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
        .btn-group {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0"><i class="bi bi-shop me-2"></i>Kelola Outlet</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#outletModal" onclick="resetForm()">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Outlet
                    </button>
                </div>

                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Nama Outlet</th>
                                <th>Alamat</th>
                                <th>Pegawai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($outlets as $outlet)
                                <tr>
                                    <td>{{ $outlet->nama }}</td>
                                    <td>{{ $outlet->alamat }}</td>
                                    <td>{{ $outlet->pegawai ? $outlet->pegawai->nama : '-' }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editOutlet({{ $outlet->id }})">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteOutlet({{ $outlet->id }}, '{{ addslashes($outlet->nama) }}')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada data outlet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Outlet -->
<div class="modal fade" id="outletModal" tabindex="-1" aria-labelledby="outletModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="outletModalLabel">Tambah Outlet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="outletForm">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="method">
                <div class="modal-body">
                    <input type="hidden" id="outlet_id" name="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Outlet</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea class="form-control" id="alamat" name="alamat" rows="4" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="pegawai_id" class="form-label">Pegawai</label>
                        <select class="form-select" id="pegawai_id" name="pegawai_id">
                            <option value="">-- Tidak Ada Pegawai --</option>
                            @foreach ($pegawai as $p)
                                <option value="{{ $p->id }}">{{ $p->nama }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    let isEdit = false;

    function resetForm() {
        document.getElementById('outletForm').reset();
        document.getElementById('outletModalLabel').textContent = 'Tambah Outlet';
        document.getElementById('submitBtn').textContent = 'Simpan';
        document.getElementById('method').value = '';
        isEdit = false;
        document.getElementById('outlet_id').value = '';
    }

    function editOutlet(id) {
        fetch(`/admin/kelola-outlet/${id}/edit`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data outlet');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            document.getElementById('nama').value = data.nama;
            document.getElementById('alamat').value = data.alamat;
            document.getElementById('pegawai_id').value = data.pegawai_id || '';
            document.getElementById('outlet_id').value = data.id;
            document.getElementById('method').value = 'PUT';
            document.getElementById('outletModalLabel').textContent = 'Edit Outlet';
            document.getElementById('submitBtn').textContent = 'Update';
            isEdit = true;
            new bootstrap.Modal(document.getElementById('outletModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data outlet: ' + error.message);
        });
    }

    function deleteOutlet(id, nama) {
        if (confirm(`Yakin ingin menghapus outlet "${nama}"?`)) {
            fetch(`/admin/kelola-outlet/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal menghapus outlet');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Gagal menghapus outlet');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus outlet: ' + error.message);
            });
        }
    }

    document.getElementById('outletForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = isEdit ? `/admin/kelola-outlet/${document.getElementById('outlet_id').value}` : '/admin/kelola-outlet';
        const method = isEdit ? 'POST' : 'POST'; // Gunakan POST untuk spoofing PUT

        fetch(url, {
            method: method,
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
                bootstrap.Modal.getInstance(document.getElementById('outletModal')).hide();
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