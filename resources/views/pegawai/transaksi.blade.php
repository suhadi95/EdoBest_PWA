@extends('layouts.app')

@section('title', 'Transaksi - {{ $outlet->nama }}')

@section('css')
<style>
    .card-body p {
        margin-bottom: 10px;
    }
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .form-check-label {
        margin-right: 15px;
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.9rem;
        }
        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cart-plus me-2"></i>{{ isset($transaksi) ? 'Detail Transaksi' : 'Transaksi Baru' }} - {{ $outlet->nama }}</h5>
                <p>Alamat: {{ $outlet->alamat }}</p>
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

                @if ($operasional || isset($transaksi))
                    <p>Transaksi ke-{{ isset($transaksi) ? $transaksi->id : $transaksiCount }}</p>

                    <h6>Detail Item</h6>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>Kemasan</th>
                                    <th>Jumlah</th>
                                    <th>Donat</th>
                                    <th>Jenis</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $items = isset($transaksi) ? $transaksi->items : $tempItems @endphp
                                @forelse ($items as $index => $item)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ ucfirst($item['kemasan'] ?? $item->kemasan) }}</td>
                                        <td>{{ $item['jumlah'] ?? $item->jumlah }}</td>
                                        <td>{{ ($item['jumlah'] ?? $item->jumlah) * ($item['donat_per_item'] ?? $item->donat_per_item) }}</td>
                                        <td>{{ ucfirst($item['tipe'] ?? $item->tipe) }}</td>
                                        <td>Rp {{ number_format($item['total_harga'] ?? $item->total_harga, 0, ',', '.') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada item transaksi.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if (!isset($transaksi))
                        <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#itemModal">
                            <i class="bi bi-plus-circle me-2"></i>Tambah Item
                        </button>
                        <form action="{{ route('pegawai.simpan-transaksi') }}" method="POST" class="mt-3">
                            @csrf
                            <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                            <input type="hidden" name="operasional_id" value="{{ $operasional->id }}">
                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran" value="tunai" checked required>
                                        <label class="form-check-label">Tunai</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran" value="qris">
                                        <label class="form-check-label">QRIS</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="metode_pembayaran" value="transfer">
                                        <label class="form-check-label">Transfer</label>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Simpan Transaksi</button>
                        </form>
                    @endif
                @else
                    <div class="alert alert-info">Operasional belum dimulai hari ini.</div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Item -->
@if (!isset($transaksi))
<div class="modal fade" id="itemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Item Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="itemForm" action="{{ route('pegawai.tambah-item') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe" value="original" checked required>
                                <label class="form-check-label">Original</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="tipe" value="klasik">
                                <label class="form-check-label">Klasik</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kemasan</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kemasan" value="mika" required>
                                <label class="form-check-label">Mika</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kemasan" value="dus1">
                                <label class="form-check-label">Dus 1</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kemasan" value="dus2">
                                <label class="form-check-label">Dus 2</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kemasan" value="dus3">
                                <label class="form-check-label">Dus 3</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="kemasan" value="box">
                                <label class="form-check-label">Box</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah" class="form-label">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('js')
@if (!isset($transaksi))
<script>
    document.getElementById('itemForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('{{ route('pegawai.tambah-item') }}', {
            method: 'POST',
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
                bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                location.reload();
            } else {
                alert('Error: ' + (data.error || 'Gagal menambahkan item'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMessage = 'Gagal menambahkan item';
            try {
                const parsedError = JSON.parse(error.message);
                errorMessage = Object.values(parsedError).flat().join(', ');
            } catch (e) {
                errorMessage = error.message;
            }
            alert('Gagal menambahkan item: ' + errorMessage);
        });
    });

    function hapusItem(index) {
        if (confirm('Hapus item ini?')) {
            fetch('{{ route('pegawai.hapus-item') }}', {
                method: 'POST',
                body: JSON.stringify({ index: index }),
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error || 'Gagal menghapus item') });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Gagal menghapus item'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal menghapus item: ' + error.message);
            });
        }
    }
</script>
@endif
@endsection