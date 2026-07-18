@extends('layouts.app')

@section('title', 'Kelola Outlet')

@section('content')
<div class="ui-page">
    <a href="javascript:history.back()" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <header class="ui-header">
        <div>
            <h1>Kelola Outlet</h1>
            <p>Tambah, edit, dan kelola data outlet</p>
        </div>
    </header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <button type="button" class="ui-primary" data-bs-toggle="modal" data-bs-target="#outletModal" onclick="resetForm()">
        <div class="ui-primary__icon"><i class="bi bi-plus-lg"></i></div>
        <div class="ui-primary__body">
            <strong>Tambah Outlet</strong>
            <span>Buat outlet baru</span>
        </div>
        <i class="bi bi-arrow-right"></i>
    </button>

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Outlet</h2>
        @forelse ($outlets as $outlet)
            @if ($loop->first)<div class="ui-menu">@endif
                <div class="ui-menu__item">
                    <div class="ui-menu__icon ui-icon--teal"><i class="bi bi-shop"></i></div>
                    <div class="ui-menu__text">
                        <strong>{{ $outlet->nama }}</strong>
                        <span>{{ $outlet->alamat }}</span>
                        <div class="mt-1 d-flex flex-wrap gap-1">
                            <span class="ui-chip ui-chip--sky">
                                <i class="bi bi-lightning-charge"></i>
                                Listrik Rp {{ number_format($outlet->biaya_listrik_harian ?? 0, 0, ',', '.') }}/hari
                            </span>
                            @forelse ($outlet->pegawais as $pegawai)
                                <span class="ui-chip ui-chip--amber">{{ $pegawai->nama }}</span>
                            @empty
                                <span class="ui-chip">Belum ada pegawai</span>
                            @endforelse
                        </div>
                    </div>
                    <div class="ui-menu__actions">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="editOutlet({{ $outlet->id }})">Edit</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteOutlet({{ $outlet->id }}, '{{ addslashes($outlet->nama) }}')">Hapus</button>
                    </div>
                </div>
            @if ($loop->last)</div>@endif
        @empty
            <div class="ui-empty">
                <i class="bi bi-shop"></i>
                <p class="mb-0">Belum ada data outlet. Klik "Tambah Outlet" untuk menambahkan.</p>
            </div>
        @endforelse
    </section>
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
                        <label for="biaya_listrik_harian" class="form-label">Biaya Listrik per Hari (Rp)</label>
                        <input type="number" class="form-control" id="biaya_listrik_harian" name="biaya_listrik_harian" min="0" step="1000" value="0" required>
                        <small class="form-text text-muted">Nominal ini ditagihkan setiap kali operasional outlet dimulai.</small>
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
            document.getElementById('biaya_listrik_harian').value = data.biaya_listrik_harian ?? 0;
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
