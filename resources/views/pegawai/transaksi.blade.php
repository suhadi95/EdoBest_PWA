@extends('layouts.app')

@section('title', '{{ isset($transaksi) ? "Detail Transaksi" : "Transaksi Baru" }} - {{ $outlet->nama }}')

@section('back-button')
<a class="ui-back" href="{{ route('pegawai.penjualan', $outlet->id) }}"><i class="bi bi-arrow-left"></i> Kembali ke Penjualan</a>
@endsection

@section('css')
<style>
    .transaction-info {
        background: #fff;
        border: 1px solid var(--border);
        border-radius: var(--border-radius);
        padding: 1rem;
        margin-bottom: 1.15rem;
    }
    .btn-action {
        padding: 0.25rem 0.5rem;
        font-size: 0.8rem;
    }
    .payment-methods {
        background: #f8f9fc;
        border-radius: var(--border-radius-sm);
        border: 1px solid var(--border);
        padding: 1rem;
        margin-bottom: 1rem;
    }
    .payment-option {
        display: inline-block;
        margin: 0.25rem;
        padding: 0.5rem 1rem;
        border: 1px solid var(--border);
        border-radius: 8px;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
    }
    .payment-option:hover {
        background: #f3f4ff;
        border-color: #c7c2f8;
    }
    .payment-option.selected {
        background: var(--brand);
        color: white;
        border-color: var(--brand);
    }
    .fixed-bottom-btn-container {
        position: fixed;
        bottom: 0;
        left: 0;
        width: 100%;
        background: white;
        padding: 0.75rem 1rem;
        box-shadow: 0 -2px 8px rgba(15, 23, 42, 0.08);
        z-index: 1050;
        text-align: center;
    }
    @media (max-width: 768px) {
        body {
            padding-bottom: 80px;
        }
    }
    @media (max-width: 576px) {
        .table-responsive {
            font-size: 0.85rem;
        }
        .btn-action {
            font-size: 0.75rem;
            padding: 0.2rem 0.4rem;
        }
        .payment-option {
            display: block;
            margin: 0.25rem 0;
            text-align: center;
        }
        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>
@endsection

@section('content')
<div class="ui-page ui-page--wide">
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

    <header class="ui-header">
        <div>
            <h1>{{ isset($transaksi) ? 'Detail Transaksi' : 'Transaksi Baru' }}</h1>
            <p>{{ $outlet->nama }} · {{ $outlet->alamat }}</p>
        </div>
    </header>

    @if ($operasional || isset($transaksi))
        <div class="transaction-info">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h6 class="mb-1">
                        <i class="bi bi-receipt me-2"></i>Transaksi ke-{{ isset($transaksi) ? ($transaksi->no_transaksi ?? $transaksi->id) : $transaksiCount }}
                    </h6>
                    @if(isset($transaksi))
                        <small class="text-muted">
                            <i class="bi bi-calendar me-1"></i>{{ $transaksi->created_at->format('d F Y H:i') }}
                        </small>
                    @endif
                </div>
                @if(isset($transaksi))
                    <div class="col-md-6 text-md-end">
                        <span class="ui-chip ui-chip--sky">
                            <i class="bi bi-credit-card me-1"></i>{{ ucfirst($transaksi->metode_pembayaran) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        <section class="ui-section">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h2 class="ui-section__title mb-0">Detail Item Transaksi</h2>
                @if (!isset($transaksi) && !empty($tempItems))
                    <button class="btn btn-outline-danger btn-sm" onclick="hapusSemuaItem()">
                        <i class="bi bi-trash me-1"></i>Hapus Semua
                    </button>
                @endif
            </div>

        <div class="ui-panel" style="padding:0; overflow:hidden;">
            <div class="card-body" style="padding:1rem;">
                @php
                    $donatPerItem = [
                        'mika' => 1,
                        'dus1' => 1,
                        'dus2' => 2,
                        'dus3' => 3,
                        'box' => 6,
                        'box12' => 12,
                        'lilin' => 0,
                    ];
                @endphp
                @php $items = isset($transaksi) ? $transaksi->items : $tempItems @endphp

                @if(count($items) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>No</th>
                                    <th>Kemasan</th>
                                    <th>Jumlah</th>
                                    <th>Donat</th>
                                    <th>Jenis</th>
                                    <th>Total</th>
                                    @if (!isset($transaksi))
                                        <th>Aksi</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($items as $index => $item)
                                    <tr>
                                        <td class="fw-semibold">{{ $index + 1 }}</td>
                                        <td>
                                            @php $kemasan = $item['kemasan'] ?? $item->kemasan @endphp
                                            <span class="badge {{ $kemasan === 'lilin' ? 'bg-warning text-dark' : 'bg-light text-dark' }}">{{ ucfirst($kemasan) }}</span>
                                        </td>
                                        <td>{{ $item['jumlah'] ?? $item->jumlah }}</td>
                                        <td class="text-info fw-semibold">
                                            {{ ($item['jumlah'] ?? $item->jumlah) * ($donatPerItem[$item['kemasan'] ?? $item->kemasan] ?? 0) }}
                                        </td>
                                        <td>
                                            @php 
                                                $tipe = $item['tipe'] ?? $item->tipe;
                                                $kemasan = $item['kemasan'] ?? $item->kemasan;
                                            @endphp
                                            @if($kemasan === 'lilin')
                                                -
                                            @elseif($tipe === 'reguler')
                                                <span class="badge bg-primary">{{ ucfirst($tipe) }}</span>
                                            @elseif($tipe === 'classic')
                                                <span class="badge bg-success">{{ ucfirst($tipe) }}</span>
                                            @else
                                                <span class="badge bg-warning">{{ ucfirst($tipe) }}</span>
                                            @endif
                                        </td>
                                        <td class="text-success fw-semibold">
                                            Rp {{ number_format($item['subtotal'] ?? $item->subtotal, 0, ',', '.') }}
                                        </td>
                                        @if (!isset($transaksi))
                                            <td>
                                                <button class="btn btn-outline-danger btn-action" onclick="hapusItem({{ $index }})">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ !isset($transaksi) ? 7 : 6 }}" class="text-center py-4">
                                            <i class="bi bi-cart-x text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">Belum ada item transaksi.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                            @if(count($items) > 0)
                                <tfoot class="table-light">
                                    <tr class="fw-semibold">
                                        <th colspan="3" class="text-end">Total Donat:</th>
                                        <th class="text-info">{{ collect($items)->sum(function($item) use ($donatPerItem) { 
                                            $jumlah = is_array($item) ? $item['jumlah'] : $item->jumlah;
                                            $kemasan = is_array($item) ? $item['kemasan'] : $item->kemasan;
                                            return $jumlah * ($donatPerItem[$kemasan] ?? 0); 
                                        }) }}</th>
                                        <th class="text-end">Total:</th>
                                        <th class="text-success">
                                            Rp {{ number_format(collect($items)->sum(function($item) { 
                                                return is_array($item) ? $item['subtotal'] : $item->subtotal; 
                                            }), 0, ',', '.') }}
                                        </th>
                                        @if (!isset($transaksi))
                                            <th></th>
                                        @endif
                                    </tr>
                                </tfoot>
                            @endif
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
                        <h6 class="text-muted mt-3">Belum ada item</h6>
                        <p class="text-muted">Tambahkan item transaksi terlebih dahulu.</p>
                    </div>
                @endif

            </div>
        </div>
        </section>

        @if(count($items) > 0)
            <div class="ui-stats">
                <div class="ui-stat">
                    <span>Total Donat</span>
                    <strong>
                        {{ collect($items)->sum(function($item) use ($donatPerItem) {
                            $jumlah = is_array($item) ? $item['jumlah'] : $item->jumlah;
                            $kemasan = is_array($item) ? $item['kemasan'] : $item->kemasan;
                            return $jumlah * ($donatPerItem[$kemasan] ?? 0);
                        }) }}
                    </strong>
                </div>
                <div class="ui-stat">
                    <span>Total Biaya</span>
                    <strong>
                        Rp {{ number_format(collect($items)->sum(function($item) {
                            return is_array($item) ? $item['subtotal'] : $item->subtotal;
                        }), 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        @endif

        @if (!isset($transaksi) && !empty($tempItems))
            <div class="ui-panel">
                <h2 class="ui-section__title">Pilih Metode Pembayaran</h2>
                <form id="paymentForm" action="{{ route('pegawai.simpan-transaksi') }}" method="POST">
                    @csrf
                    <input type="hidden" name="outlet_id" value="{{ $outlet->id }}">
                    <input type="hidden" name="operasional_id" value="{{ $operasional->id }}">

                    <div class="payment-methods">
                        <div class="d-flex justify-content-center mb-2">
                            <div class="btn-group" role="group" aria-label="Metode Pembayaran Atas">
                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeTunai" value="tunai" autocomplete="off" checked required>
                                <label class="btn btn-outline-primary" for="metodeTunai">
                                    <i class="bi bi-cash me-1"></i>Tunai
                                </label>

                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeQris" value="qris" autocomplete="off">
                                <label class="btn btn-outline-primary" for="metodeQris">
                                    <i class="bi bi-qr-code me-1"></i>QRIS
                                </label>

                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeTransfer" value="transfer" autocomplete="off">
                                <label class="btn btn-outline-primary" for="metodeTransfer">
                                    <i class="bi bi-bank me-1"></i>Transfer
                                </label>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div class="btn-group" role="group" aria-label="Metode Pembayaran Bawah">
                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeGrabfood" value="grabfood" autocomplete="off">
                                <label class="btn btn-outline-primary" for="metodeGrabfood">
                                    <i class="bi bi-scooter me-1"></i>Grabfood
                                </label>

                                <input type="radio" class="btn-check" name="metode_pembayaran" id="metodeGofood" value="gofood" autocomplete="off">
                                <label class="btn btn-outline-primary" for="metodeGofood">
                                    <i class="bi bi-scooter me-1"></i>Gofood
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-check-circle me-2"></i>Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        @endif
    @endif

    @if (!isset($transaksi))
        <div class="fixed-bottom-btn-container d-md-none">
            <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#itemModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Item
            </button>
        </div>
        <div class="d-none d-md-block text-center mt-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#itemModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Item
            </button>
        </div>
    @endif
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
                    <form id="itemForm">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label d-block mb-2">Produk</label>
                                <div class="btn-group flex-wrap" role="group" aria-label="Kemasan">
                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanMika" value="mika" autocomplete="off" checked required onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-primary" for="kemasanMika">Mika</label>

                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanDus1" value="dus1" autocomplete="off" onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-primary" for="kemasanDus1">Dus 1</label>

                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanDus2" value="dus2" autocomplete="off" onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-primary" for="kemasanDus2">Dus 2</label>

                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanDus3" value="dus3" autocomplete="off" onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-primary" for="kemasanDus3">Dus 3</label>

                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanBox" value="box" autocomplete="off" onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-primary" for="kemasanBox">Box</label>

                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanBox12" value="box12" autocomplete="off" onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-primary" for="kemasanBox12">Box 12</label>

                                    <input type="radio" class="btn-check" name="kemasan" id="kemasanLilin" value="lilin" autocomplete="off" onchange="toggleTipeDonat()">
                                    <label class="btn btn-outline-warning" for="kemasanLilin">Lilin</label>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-6">
                                    <label for="jumlah" class="form-label">Jumlah</label>
                                    <input type="number" class="form-control" name="jumlah" min="1" required>
                                </div>
                                <div class="col-6" id="tipeDonatContainer">
                                    <label class="form-label d-block mb-2">Jenis Donat</label>
                                    <div class="btn-group-vertical w-100" role="group" aria-label="Jenis Donat">
                                        <input type="radio" class="btn-check" name="tipe" id="tipeReguler" value="reguler" autocomplete="off" checked>
                                        <label class="btn btn-outline-success" for="tipeReguler">Reguler</label>

                                        <input type="radio" class="btn-check" name="tipe" id="tipeClassic" value="classic" autocomplete="off">
                                        <label class="btn btn-outline-success" for="tipeClassic">Classic</label>

                                        <input type="radio" class="btn-check" name="tipe" id="tipeCustom" value="custom" autocomplete="off">
                                        <label class="btn btn-outline-success" for="tipeCustom">Custom</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tambah</button>
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
        const donatPerItem = @json($donatPerItem);
        let currentItems = @json($tempItems);

        // Update totals on page load
        updateTotals();

        // Function to toggle Jenis Donat visibility
        function toggleTipeDonat() {
            const kemasanLilin = document.getElementById('kemasanLilin');
            const tipeDonatContainer = document.getElementById('tipeDonatContainer');
            
            if (kemasanLilin && kemasanLilin.checked) {
                // Hide Jenis Donat for Lilin
                tipeDonatContainer.style.display = 'none';
                // Remove required from tipe radio buttons
                document.querySelectorAll('input[name="tipe"]').forEach(input => {
                    input.removeAttribute('required');
                });
            } else {
                // Show Jenis Donat for other products
                tipeDonatContainer.style.display = 'block';
                // Add required back
                document.getElementById('tipeReguler').setAttribute('required', 'required');
            }
        }

        // Initialize on page load
        toggleTipeDonat();

        function updateTotals() {
            const totalDonat = currentItems.reduce((sum, item) => {
                const jumlah = item.jumlah || item['jumlah'];
                const kemasan = item.kemasan || item['kemasan'];
                return sum + (jumlah * (donatPerItem[kemasan] || 0));
            }, 0);
            const totalHarga = currentItems.reduce((sum, item) => {
                return sum + (item.subtotal || item['subtotal']);
            }, 0);

            // Update table footer
            const tfoot = document.querySelector('table tfoot tr');
            if (tfoot && tfoot.cells && tfoot.cells.length >= 4) {
                tfoot.cells[1].textContent = totalDonat;
                tfoot.cells[3].textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
            }

            // Update summary cards
            const donatCard = document.querySelector('.card-body h3.text-info.fw-bold');
            if (donatCard) donatCard.textContent = totalDonat;

            const hargaCard = document.querySelector('.card-body h3.text-success.fw-bold');
            if (hargaCard) hargaCard.textContent = 'Rp ' + totalHarga.toLocaleString('id-ID');
        }

        function addItemToTable(item, index) {
            const tbody = document.querySelector('table tbody');
            if (!tbody) return;

            const row = document.createElement('tr');
            const kemasan = item.kemasan || item['kemasan'];
            const jumlah = item.jumlah || item['jumlah'];
            const tipe = item.tipe || item['tipe'];
            const subtotal = item.subtotal || item['subtotal'];
            
            const displayTipe = kemasan === 'lilin' ? '-' : `<span class="badge bg-${tipe === 'reguler' ? 'primary' : tipe === 'classic' ? 'success' : 'warning'}">${tipe.charAt(0).toUpperCase() + tipe.slice(1)}</span>`;
            
            row.innerHTML = `
                <td class="fw-semibold">${index + 1}</td>
                <td><span class="badge ${kemasan === 'lilin' ? 'bg-warning text-dark' : 'bg-light text-dark'}">${kemasan.charAt(0).toUpperCase() + kemasan.slice(1)}</span></td>
                <td>${jumlah}</td>
                <td class="text-info fw-semibold">${jumlah * (donatPerItem[kemasan] || 0)}</td>
                <td>${displayTipe}</td>
                <td class="text-success fw-semibold">Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td><button class="btn btn-outline-danger btn-action" onclick="hapusItem(${index})"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(row);
        }

        function removeItemFromTable(index) {
            const tbody = document.querySelector('table tbody');
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            if (rows[index]) {
                rows[index].remove();
                // Reindex remaining rows
                rows.forEach((row, i) => {
                    if (i >= index) {
                        row.cells[0].textContent = i + 1;
                        const btn = row.querySelector('button[onclick*="hapusItem"]');
                        if (btn) btn.setAttribute('onclick', `hapusItem(${i})`);
                    }
                });
            }
        }

        function clearTable() {
            const tbody = document.querySelector('table tbody');
            if (tbody) tbody.innerHTML = '';
            currentItems = [];
            updateTotals();
        }

        function resetForm() {
            // Reset form inputs
            document.getElementById('itemForm').reset();
            
            // Reset radio buttons to default values
            document.getElementById('kemasanMika').checked = true;
            document.getElementById('tipeReguler').checked = true;
            
            // Show tipe donat container again
            toggleTipeDonat();
            
            // Clear any validation states
            const inputs = document.querySelectorAll('#itemForm input');
            inputs.forEach(input => {
                input.classList.remove('is-valid', 'is-invalid');
            });
        }

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
                    return response.json().then(err => {
                        throw new Error(JSON.stringify(err.error));
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('itemModal')).hide();
                    if (data.reload) {
                        location.reload();
                    } else {
                        currentItems.push(data.item);
                        addItemToTable(data.item, currentItems.length - 1);
                        updateTotals();
                        resetForm(); // Reset form setelah item berhasil ditambahkan
                    }
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
                        return response.json().then(err => {
                            throw new Error(err.error || 'Gagal menghapus item');
                        });
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

        function hapusSemuaItem() {
            if (confirm('Hapus semua item transaksi?')) {
                fetch('{{ route('pegawai.hapus-semua-item') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        return response.json().then(err => {
                            throw new Error(err.error || 'Gagal menghapus semua item');
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Gagal menghapus semua item'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal menghapus semua item: ' + error.message);
                });
            }
        }
    </script>
@endif
@endsection
