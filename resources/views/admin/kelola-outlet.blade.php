@extends('layouts.app')

@section('title', 'Kelola Outlet')

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
    
    .outlet-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }
    
    .outlet-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }
    
    .outlet-card .card-body {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        min-height: 120px;
    }
    
    .outlet-info {
        flex: 1;
        margin-right: 1rem;
    }
    
    /* Memastikan layout horizontal di layar besar */
    @media (min-width: 577px) {
        .outlet-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .outlet-info {
            flex: 1;
            margin-right: 1rem;
        }
        
        .outlet-actions {
            flex-direction: column;
            align-items: stretch;
            min-width: 120px;
            justify-content: stretch;
        }
    }
    
    .outlet-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }
    
    .outlet-detail {
        font-size: 0.9rem;
        color: var(--secondary-color);
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .outlet-info-inline {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .outlet-name-inline {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--dark-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.25rem;
    }
    
    .outlet-address-inline {
        font-size: 0.9rem;
        color: var(--secondary-color);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .pegawai-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.25rem;
        align-items: center;
    }
    
    .pegawai-badge {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        color: #856404;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        border: 1px solid #ffeaa7;
    }
    
    .outlet-actions {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 120px;
        width: 120px;
        height: 100%;
        align-items: stretch;
        justify-content: stretch;
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
        
        .outlet-card .card-body {
            padding: 1.25rem;
        }
        
        .outlet-actions {
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
        
        .outlet-card .card-body {
            padding: 1rem;
        }
        
        /* Tetap horizontal di mobile */
        .outlet-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .outlet-info {
            flex: 1;
            margin-right: 0.5rem;
        }
        
        .outlet-name-inline {
            font-size: 0.95rem;
        }
        
        .outlet-address-inline {
            font-size: 0.8rem;
        }
        
        .outlet-detail {
            font-size: 0.8rem;
        }
        
        .pegawai-badge {
            font-size: 0.7rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* Tetap horizontal di layar sangat kecil */
        .outlet-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .outlet-actions {
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
        
        .modal-body textarea {
            min-height: 80px;
        }
        
        .modal-body .form-select[multiple] {
            min-height: 120px;
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
        
        .outlet-card .card-body {
            padding: 0.75rem;
        }
        
        /* Tetap horizontal di layar sangat kecil */
        .outlet-card .d-flex {
            flex-direction: row;
            align-items: stretch;
            height: 100%;
        }
        
        .outlet-info {
            flex: 1;
            margin-right: 0.25rem;
        }
        
        .outlet-name-inline {
            font-size: 0.9rem;
        }
        
        .outlet-address-inline {
            font-size: 0.75rem;
        }
        
        .outlet-detail {
            font-size: 0.75rem;
        }
        
        .action-btn {
            padding: 0.5rem 0.4rem;
            font-size: 0.75rem;
            flex: 1;
            width: 100%;
        }
        
        .pegawai-badge {
            font-size: 0.6rem;
            padding: 0.1rem 0.25rem;
        }
        
        .outlet-actions {
            width: 80px;
            min-width: 80px;
            justify-content: stretch;
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
            <i class="bi bi-shop-window"></i>
            Kelola Outlet
        </h1>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Add Outlet Button -->
    <div class="add-btn-card">
        <button class="add-btn" data-bs-toggle="modal" data-bs-target="#outletModal" onclick="resetForm()">
            <i class="bi bi-plus-circle" style="font-size: 1.5rem;"></i>
            <span>Tambah Outlet Baru</span>
        </button>
    </div>

    <!-- Outlet List -->
    <div class="row">
        @forelse ($outlets as $outlet)
            <div class="col-12">
                <div class="outlet-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="outlet-info">
                                <div class="outlet-info-inline">
                                <div class="outlet-name-inline">
                                    <i class="bi bi-shop"></i>
                                    {{ $outlet->nama }}
                                </div>
                                <div class="outlet-address-inline">
                                    <i class="bi bi-geo-alt-fill"></i>
                                    <span>{{ $outlet->alamat }}</span>
                                </div>
                                <div class="outlet-detail">
                                    <i class="bi bi-people-fill"></i>
                                    <div>
                                        @if($outlet->pegawais->count() > 0)
                                            <div class="pegawai-list">
                                                @foreach($outlet->pegawais as $pegawai)
                                                    <span class="pegawai-badge">{{ $pegawai->nama }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">Belum ada pegawai ditugaskan</span>
                                        @endif
                                    </div>
                                </div>
                                </div>
                            </div>
                            <div class="outlet-actions">
                                <button class="action-btn btn-edit" onclick="editOutlet({{ $outlet->id }})">
                                    <i class="bi bi-pencil-square"></i>
                                    <span>Edit</span>
                                </button>
                                <button class="action-btn btn-delete" onclick="deleteOutlet({{ $outlet->id }}, '{{ addslashes($outlet->nama) }}')">
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
                    <i class="bi bi-shop"></i>
                    <h6>Belum Ada Data Outlet</h6>
                    <p>Klik tombol "Tambah Outlet Baru" untuk menambahkan outlet pertama.</p>
                </div>
            </div>
        @endforelse
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
                        <label for="pegawai_ids" class="form-label">Pegawai</label>
                            <select class="form-select" id="pegawai_ids" name="pegawai_ids[]" multiple>
                                @foreach ($pegawais as $p)
                                    <option value="{{ $p->id }}">{{ $p->nama }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Tekan Ctrl (atau Cmd) untuk memilih beberapa pegawai.</small>
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
            // Set selected pegawai
            const pegawaiSelect = document.getElementById('pegawai_ids');
            Array.from(pegawaiSelect.options).forEach(option => {
                option.selected = data.pegawai_ids.includes(parseInt(option.value));
            });
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
                if (typeof parsedError === 'string') {
                    errorMessage = parsedError;
                } else {
                    errorMessage = Object.values(parsedError).flat().join(', ');
                }
            } catch (e) {
                errorMessage = error.message;
            }
            alert('Gagal menyimpan data: ' + errorMessage);
        });
    });
</script>
@endsection