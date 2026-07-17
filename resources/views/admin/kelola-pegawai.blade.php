@extends('layouts.app')

@section('title', 'Kelola Pegawai')

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
    
    .add-btn-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 2rem;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .add-btn-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .add-btn {
        width: 100%;
        padding: 1.5rem;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        border-radius: var(--border-radius);
        font-weight: 600;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .add-btn:hover {
        color: white;
        transform: translateY(-1px);
        background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    }
    
    .pegawai-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    
    .pegawai-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .pegawai-card .card-body {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        min-height: 120px;
    }
    
    .pegawai-info {
        flex: 1;
        margin-right: 1rem;
    }
    
    .pegawai-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .pegawai-detail {
        font-size: 0.9rem;
        color: var(--secondary-color);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .pegawai-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        padding: 0.375rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 0.5rem;
    }
    
    .pegawai-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 120px;
        width: 120px;
        height: 100%;
        align-items: stretch;
        justify-content: stretch;
    }
    
    /* Memastikan layout horizontal di layar besar */
    @media (min-width: 577px) {
        .pegawai-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .pegawai-info {
            flex: 1;
            margin-right: 1rem;
        }
        
        .pegawai-actions {
            flex-direction: column;
            align-items: stretch;
            min-width: 120px;
            justify-content: stretch;
        }
    }
    
    .action-btn {
        padding: 0.75rem 1rem;
        border-radius: 6px;
        font-size: 0.9rem;
        font-weight: 500;
        border: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        flex: 1;
        width: 100%;
    }
    
    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: var(--box-shadow-sm);
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
    }
    
    .btn-edit:hover {
        background: linear-gradient(135deg, #138496 0%, #0f6674 100%);
        color: white;
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
        box-shadow: var(--box-shadow);
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
    
    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .page-title {
            font-size: 1.25rem;
        }
        
        .add-btn {
            padding: 1.25rem;
            font-size: 1rem;
        }
        
        .pegawai-card .card-body {
            padding: 1rem;
        }
        
        .pegawai-actions {
            flex-direction: column;
            width: 100px;
            min-width: 100px;
            justify-content: stretch;
        }
        
        .action-btn {
            flex: 1;
            padding: 0.75rem 0.75rem;
            font-size: 0.85rem;
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem;
            padding-right: 0.5rem;
        }
        
        .page-header {
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        
        .page-title {
            font-size: 1.1rem;
            flex-direction: column;
            gap: 0.25rem;
        }
        
        .add-btn {
            padding: 1rem;
            font-size: 0.95rem;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .add-btn i {
            font-size: 1.25rem;
        }
        
        .pegawai-card .card-body {
            padding: 0.75rem;
        }
        
        /* Tetap horizontal di mobile */
        .pegawai-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .pegawai-info {
            flex: 1;
            margin-right: 0.5rem;
        }
        
        .pegawai-name {
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        
        .pegawai-detail {
            font-size: 0.8rem;
            margin-bottom: 0.25rem;
        }
        
        .pegawai-detail i {
            width: 14px;
            flex-shrink: 0;
        }
        
        .pegawai-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
            margin-top: 0.5rem;
        }
        
        .pegawai-actions {
            flex-direction: column;
            gap: 0.5rem;
            width: 90px;
            min-width: 90px;
            align-items: stretch;
            justify-content: stretch;
        }
        
        .action-btn {
            padding: 0.6rem 0.5rem;
            font-size: 0.8rem;
            flex: 1;
            width: 100%;
        }
        
        .action-btn span {
            display: none;
        }
        
        .action-btn i {
            font-size: 0.9rem;
        }
        
        .empty-state {
            padding: 2rem 1rem;
        }
        
        .empty-state i {
            font-size: 2.5rem;
        }
        
        .empty-state h6 {
            font-size: 1rem;
        }
        
        .empty-state p {
            font-size: 0.85rem;
        }
        
        /* Modal responsive */
        .modal-dialog {
            margin: 0.5rem;
            max-width: calc(100% - 1rem);
        }
        
        .modal-body .row .col-md-6 {
            margin-bottom: 1rem;
        }
        
        .modal-body .row .col-md-6:last-child {
            margin-bottom: 0;
        }
        
        /* Pastikan input tambahan gaji dan target tetap dalam satu baris di mobile */
        .modal-body .row .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    
    @media (max-width: 480px) {
        .page-header {
            padding: 0.5rem;
        }
        
        .page-title {
            font-size: 1rem;
        }
        
        .add-btn {
            padding: 0.875rem;
            font-size: 0.9rem;
        }
        
        .pegawai-card .card-body {
            padding: 0.5rem;
        }
        
        /* Tetap horizontal di layar sangat kecil */
        .pegawai-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .pegawai-info {
            flex: 1;
            margin-right: 0.25rem;
        }
        
        .pegawai-name {
            font-size: 0.9rem;
        }
        
        .pegawai-detail {
            font-size: 0.75rem;
        }
        
        .pegawai-actions {
            width: 80px;
            min-width: 80px;
            justify-content: stretch;
        }
        
        .action-btn {
            padding: 0.5rem 0.4rem;
            font-size: 0.75rem;
            flex: 1;
            width: 100%;
        }
        
        /* Pastikan input tambahan gaji dan target tetap dalam satu baris di layar sangat kecil */
        .modal-body .row .col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
            padding-left: 0.25rem;
            padding-right: 0.25rem;
        }
        
        .modal-body .row .col-md-6:first-child {
            padding-right: 0.25rem;
        }
        
        .modal-body .row .col-md-6:last-child {
            padding-left: 0.25rem;
        }
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
            <i class="bi bi-people-fill"></i>
            Kelola Pegawai
        </h1>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add Employee Button -->
    <div class="add-btn-card">
        <button class="add-btn" data-bs-toggle="modal" data-bs-target="#pegawaiModal" onclick="resetForm()">
            <i class="bi bi-plus-circle" style="font-size: 1.5rem;"></i>
            <span>Tambah Pegawai Baru</span>
        </button>
    </div>

    <!-- Employee List -->
    <div class="row">
        @forelse ($pegawai as $p)
            <div class="col-12">
                <div class="pegawai-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="pegawai-info">
                                <h6 class="pegawai-name">{{ $p->nama }}</h6>
                                <div class="pegawai-detail">
                                    <i class="bi bi-person-badge"></i>
                                    <span>{{ $p->username }}</span>
                                </div>
                                <div class="pegawai-detail">
                                    <i class="bi bi-shop"></i>
                                    <span>{{ $p->outlet->nama ?? 'Belum ditugaskan' }}</span>
                                </div>
                                <div class="pegawai-detail">
                                    <i class="bi bi-cash"></i>
                                    <span>Gaji Harian: Rp {{ number_format($p->gaji_harian ?? 0, 0, ',', '.') }}</span>
                                </div>
                                <span class="pegawai-badge bg-{{ $p->role === 'admin' ? 'success' : 'warning' }}">
                                    <i class="bi bi-{{ $p->role === 'admin' ? 'shield-fill' : 'person-fill' }}"></i>
                                    {{ ucfirst($p->role) }}
                                </span>
                            </div>
                            <div class="pegawai-actions">
                                <button class="action-btn btn-edit" onclick="editPegawai({{ $p->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Edit</span>
                                </button>
                                <button class="action-btn btn-delete" onclick="deletePegawai({{ $p->id }}, '{{ addslashes($p->nama) }}')">
                                    <i class="bi bi-trash3"></i>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <i class="bi bi-people"></i>
                    <h6>Belum Ada Data Pegawai</h6>
                    <p>Klik tombol "Tambah Pegawai Baru" untuk menambahkan pegawai pertama.</p>
                </div>
            </div>
        @endforelse
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
                    <div class="mb-3">
                        <label for="gaji_harian" class="form-label">Gaji Harian (Rp)</label>
                        <input type="number" class="form-control" id="gaji_harian" name="gaji_harian" min="0" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambahan_gaji_1" class="form-label">Tambahan Gaji 1 (Rp)</label>
                            <input type="number" class="form-control" id="tambahan_gaji_1" name="tambahan_gaji_1" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="target_1" class="form-label">Target 1 (Donut)</label>
                            <input type="number" class="form-control" id="target_1" name="target_1" min="0">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambahan_gaji_2" class="form-label">Tambahan Gaji 2 (Rp)</label>
                            <input type="number" class="form-control" id="tambahan_gaji_2" name="tambahan_gaji_2" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="target_2" class="form-label">Target 2 (Donut)</label>
                            <input type="number" class="form-control" id="target_2" name="target_2" min="0">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambahan_gaji_3" class="form-label">Tambahan Gaji 3 (Rp)</label>
                            <input type="number" class="form-control" id="tambahan_gaji_3" name="tambahan_gaji_3" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="target_3" class="form-label">Target 3 (Donut)</label>
                            <input type="number" class="form-control" id="target_3" name="target_3" min="0">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tambahan_gaji_4" class="form-label">Tambahan Gaji 4 (Rp)</label>
                            <input type="number" class="form-control" id="tambahan_gaji_4" name="tambahan_gaji_4" min="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="target_4" class="form-label">Target 4 (Donut)</label>
                            <input type="number" class="form-control" id="target_4" name="target_4" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bonus_nominal" class="form-label">Bonus Nominal (Rp)</label>
                        <input type="number" class="form-control" id="bonus_nominal" name="bonus_nominal" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="bonus_syarat" class="form-label">Syarat Bonus (Donut)</label>
                        <input type="number" class="form-control" id="bonus_syarat" name="bonus_syarat" min="0" required>
                    </div>
                    
                    <!-- Hidden input untuk role pegawai -->
                    <input type="hidden" id="role" name="role" value="pegawai">
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
            document.getElementById('tambahan_gaji_1').value = data.tambahan_gaji_1;
            document.getElementById('target_1').value = data.target_1;
            document.getElementById('tambahan_gaji_2').value = data.tambahan_gaji_2;
            document.getElementById('target_2').value = data.target_2;
            document.getElementById('tambahan_gaji_3').value = data.tambahan_gaji_3;
            document.getElementById('target_3').value = data.target_3;
            document.getElementById('tambahan_gaji_4').value = data.tambahan_gaji_4;
            document.getElementById('target_4').value = data.target_4;
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
