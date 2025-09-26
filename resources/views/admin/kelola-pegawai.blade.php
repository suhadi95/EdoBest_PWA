@extends('layouts.app')

@section('title', 'Kelola Pegawai')

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
                    <h5 class="card-title mb-0"><i class="bi bi-people me-2"></i>Kelola Pegawai</h5>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pegawaiModal" onclick="resetForm()">
                        <i class="bi bi-plus-circle me-2"></i>Tambah Pegawai
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
                                <th>Nama</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pegawai as $p)
                                <tr>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->username }}</td>
                                    <td><span class="badge bg-{{ $p->role === 'admin' ? 'danger' : 'primary' }}">{{ ucfirst($p->role) }}</span></td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-sm btn-outline-primary" onclick="editPegawai({{ $p->id }})">
                                                <i class="bi bi-pencil"></i> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deletePegawai({{ $p->id }}, '{{ addslashes($p->nama) }}')">
                                                <i class="bi bi-trash"></i> Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data pegawai.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah/Edit Pegawai -->
<div class="modal fade" id="pegawaiModal" tabindex="-1" aria-labelledby="pegawaiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="pegawaiModalLabel">Tambah Pegawai</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pegawaiForm">
                @csrf
                <input type="hidden" name="_method" value="PUT" id="method">
                <div class="modal-body">
                    <input type="hidden" id="pegawai_id" name="id">
                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama Pegawai</label>
                        <input type="text" class="form-control" id="nama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="gaji_harian" class="form-label">Gaji Harian (Rp)</label>
                            <input type="number" class="form-control" id="gaji_harian" name="gaji_harian" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bonus_nominal" class="form-label">Bonus Nominal (Rp)</label>
                            <input type="number" class="form-control" id="bonus_nominal" name="bonus_nominal" min="0" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="bonus_syarat" class="form-label">Syarat Bonus (Donut)</label>
                            <input type="number" class="form-control" id="bonus_syarat" name="bonus_syarat" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="pegawai">Pegawai</option>
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
        document.getElementById('pegawaiForm').reset();
        document.getElementById('pegawaiModalLabel').textContent = 'Tambah Pegawai';
        document.getElementById('submitBtn').textContent = 'Simpan';
        document.getElementById('method').value = '';
        isEdit = false;
        document.getElementById('pegawai_id').value = '';
    }

    function editPegawai(id) {
        fetch(`/admin/kelola-pegawai/${id}/edit`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Gagal mengambil data pegawai');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            document.getElementById('nama').value = data.nama;
            document.getElementById('username').value = data.username;
            document.getElementById('gaji_harian').value = data.gaji_harian;
            document.getElementById('bonus_nominal').value = data.bonus_nominal;
            document.getElementById('bonus_syarat').value = data.bonus_syarat;
            document.getElementById('role').value = data.role;
            document.getElementById('pegawai_id').value = data.id;
            document.getElementById('method').value = 'PUT';
            document.getElementById('pegawaiModalLabel').textContent = 'Edit Pegawai';
            document.getElementById('submitBtn').textContent = 'Update';
            isEdit = true;
            new bootstrap.Modal(document.getElementById('pegawaiModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal mengambil data pegawai: ' + error.message);
        });
    }

    function deletePegawai(id, nama) {
        if (confirm(`Yakin ingin menghapus pegawai "${nama}"?`)) {
            fetch(`/admin/kelola-pegawai/${id}`, {
                method: 'DELETE',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Gagal menghapus pegawai');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.error || 'Gagal menghapus pegawai');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus pegawai: ' + error.message);
            });
        }
    }

    document.getElementById('pegawaiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const url = isEdit ? `/admin/kelola-pegawai/${document.getElementById('pegawai_id').value}` : '/admin/kelola-pegawai';
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
                bootstrap.Modal.getInstance(document.getElementById('pegawaiModal')).hide();
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