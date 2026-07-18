@extends('layouts.app')

@section('title', 'Kelola Pegawai')

@section('content')
<div class="ui-page">
    <a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <header class="ui-header">
        <div>
            <h1>Kelola Pegawai</h1>
            <p>Tambah, edit, dan kelola data pegawai</p>
        </div>
    </header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <button type="button" class="ui-primary" data-bs-toggle="modal" data-bs-target="#pegawaiModal" onclick="resetForm()">
        <div class="ui-primary__icon"><i class="bi bi-plus-lg"></i></div>
        <div class="ui-primary__body">
            <strong>Tambah Pegawai</strong>
            <span>Buat akun pegawai baru</span>
        </div>
        <i class="bi bi-arrow-right"></i>
    </button>

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Pegawai</h2>
        @forelse ($pegawai as $p)
            @if ($loop->first)<div class="ui-menu">@endif
                <div class="ui-menu__item">
                    <div class="ui-menu__icon ui-icon--violet"><i class="bi bi-person"></i></div>
                    <div class="ui-menu__text">
                        <strong>{{ $p->nama }}</strong>
                        <span>{{ $p->username }} · {{ $p->outlet->nama ?? 'Belum ditugaskan' }} · Gaji Rp {{ number_format($p->gaji_harian ?? 0, 0, ',', '.') }}</span>
                        <div class="mt-1">
                            <span class="ui-chip {{ $p->role === 'admin' ? 'ui-chip--green' : 'ui-chip--amber' }}">
                                <i class="bi bi-{{ $p->role === 'admin' ? 'shield-fill' : 'person-fill' }}"></i>
                                {{ ucfirst($p->role) }}
                            </span>
                        </div>
                    </div>
                    <div class="ui-menu__actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editPegawai({{ $p->id }})">Edit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePegawai({{ $p->id }}, '{{ addslashes($p->nama) }}')">Hapus</button>
                    </div>
                </div>
            @if ($loop->last)</div>@endif
        @empty
            <div class="ui-empty">
                <i class="bi bi-people"></i>
                <p class="mb-0">Belum ada data pegawai. Klik "Tambah Pegawai" untuk menambahkan.</p>
            </div>
        @endforelse
    </section>
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
