@extends('layouts.app')

@section('title', 'Detail Penggajian - ' . $pegawai->nama)

@section('css')
<style>
    .gaji-breakdown {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
    }
    .gaji-total {
        background-color: #e3f2fd;
        border: 2px solid #2196f3;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
    }
    .status-paid {
        color: #4caf50;
        font-weight: bold;
    }
    .status-unpaid {
        color: #f44336;
        font-weight: bold;
    }
    /* Fix modal z-index to be above navbar */
    .modal {
        z-index: 1055 !important;
    }
    
    /* Add a specific class to the Rekap Gaji table */
    .rekap-gaji-table th,
    .rekap-gaji-table td {
        padding: 0.75rem !important;
    }
    
    @media (max-width: 767.98px) {
        .rekap-gaji-table {
            table-layout: fixed !important;
        }
        
        .rekap-gaji-table th:first-child,
        .rekap-gaji-table td:first-child {
            width: 60% !important;
        }
        
        .rekap-gaji-table th:last-child,
        .rekap-gaji-table td:last-child {
            width: 40% !important;
        }
    }

    /* Pagination - Sesuai Tema Website */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
        margin-bottom: 1rem;
    }

    .pagination-wrapper .pagination {
        margin: 0;
    }

    .pagination-wrapper .page-item {
        margin: 0 0.25rem;
    }

    .pagination-wrapper .page-link {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        color: #495057;
        padding: 0.625rem 0.875rem;
        transition: all 0.2s ease;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        font-weight: 500;
    }

    .pagination-wrapper .page-link:hover {
        color: #198754;
        background-color: #f8f9fa;
        border-color: #198754;
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.15);
    }

    .pagination-wrapper .page-item.active .page-link {
        color: #ffffff;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border-color: #198754;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.25);
    }

    .pagination-wrapper .page-item.active .page-link:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(25, 135, 84, 0.3);
    }

    .pagination-wrapper .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
        opacity: 0.6;
        pointer-events: none;
        box-shadow: none;
    }

    .pagination-wrapper .page-item.disabled .page-link:hover {
        transform: none;
    }

    /* Responsive Pagination */
    @media (max-width: 768px) {
        .pagination-wrapper {
            margin-top: 1.5rem;
            margin-bottom: 0.75rem;
        }

        .pagination-wrapper .pagination {
            flex-wrap: wrap;
        }

        .pagination-wrapper .page-item {
            margin: 0 0.2rem 0.5rem 0.2rem;
        }

        .pagination-wrapper .page-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    }

    @media (max-width: 576px) {
        .pagination-wrapper {
            margin-top: 1rem;
            margin-bottom: 0.5rem;
        }

        .pagination-wrapper .pagination {
            flex-wrap: wrap;
        }

        .pagination-wrapper .page-item {
            margin: 0 0.15rem 0.4rem 0.15rem;
        }

        .pagination-wrapper .page-link {
            padding: 0.45rem 0.65rem;
            font-size: 0.8rem;
        }
    }
</style>
@endsection

@section('js')
<script>
$(document).ready(function() {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Inisialisasi periode awal saat halaman dimuat
    let periodeAwal = {
        mulai: $('#tanggal-mulai').val(),
        akhir: $('#tanggal-akhir').val()
    };

    $('#validate-gaji').click(function() {
        const tanggalMulai = $('#tanggal-mulai').val();
        const tanggalAkhir = $('#tanggal-akhir').val();
        const periodeKeterangan = $('#periode-keterangan').val();
        const gajiTotal = $('#gaji-total').text().replace('Rp ', '').replace(/\./g, '').replace(',', '');
        const pegawaiId = {{ $pegawai->id }};

        if (!tanggalMulai || !tanggalAkhir) {
            alert('Mohon isi tanggal mulai dan tanggal akhir periode penggajian.');
            return;
        }

        if (confirm('Apakah Anda yakin ingin memvalidasi gaji sebesar Rp ' + formatNumber(gajiTotal) + ' untuk periode ' + tanggalMulai + ' hingga ' + tanggalAkhir + '?')) {
            $.ajax({
                url: '/admin/penggajian/' + pegawaiId + '/validate',
                type: 'POST',
                data: {
                    tanggal_mulai: tanggalMulai,
                    tanggal_akhir: tanggalAkhir,
                    periode_keterangan: periodeKeterangan,
                    gaji_total: gajiTotal,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    alert('Gaji berhasil divalidasi!');
                    location.reload();
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        alert('Error: ' + (response.error || 'Terjadi kesalahan'));
                    } catch (e) {
                        alert('Terjadi kesalahan saat memproses permintaan');
                    }
                }
            });
        }
    });

    // Handle klik tombol Hitung Ulang Gaji
    $('#hitung-ulang').click(function() {
        const tanggalMulai = $('#tanggal-mulai').val();
        const tanggalAkhir = $('#tanggal-akhir').val();
        const pegawaiId = {{ $pegawai->id }};

        console.log('Values:', { tanggalMulai, tanggalAkhir, pegawaiId });

        if (!tanggalMulai || !tanggalAkhir) {
            alert('Mohon isi tanggal mulai dan tanggal akhir periode penggajian.');
            return;
        }

        if (new Date(tanggalMulai) > new Date(tanggalAkhir)) {
            alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir.');
            return;
        }

        // Tampilkan loading
        $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split"></i> Menghitung...');

        console.log('Sending request:', {
            url: '/admin/penggajian/' + pegawaiId + '/calculate',
            tanggal_mulai: tanggalMulai,
            tanggal_akhir: tanggalAkhir
        });

        $.ajax({
            url: '/admin/penggajian/' + pegawaiId + '/calculate',
            type: 'POST',
            data: {
                tanggal_mulai: tanggalMulai,
                tanggal_akhir: tanggalAkhir,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                console.log('Response received:', data);
                if (data.error) {
                    alert('Error: ' + data.error);
                } else {
                    // Update tampilan dengan data baru
                    updateKalkulasiDisplay(data);
                }
                
                // Reset tombol
                $('#hitung-ulang').prop('disabled', false).html('<i class="bi bi-calculator"></i> Hitung Ulang Gaji');
            },
            error: function(xhr) {
                console.log('Error response:', xhr);
                console.log('Status:', xhr.status);
                console.log('Response text:', xhr.responseText);
                try {
                    const response = JSON.parse(xhr.responseText);
                    alert('Error: ' + (response.error || 'Terjadi kesalahan saat menghitung gaji'));
                } catch (e) {
                    alert('Terjadi kesalahan saat menghitung gaji');
                }
                $('#hitung-ulang').prop('disabled', false).html('<i class="bi bi-calculator"></i> Hitung Ulang Gaji');
            }
        });
    });

    // Handle klik tombol Detail Gaji
    $(document).on('click', '.btn-detail-gaji', function() {
        const pegawaiId = {{ $pegawai->id }};
        const gajiId = $(this).data('gaji-id');
        const modal = $('#detailGajiModal');

        // Load detail gaji via AJAX
        $.ajax({
            url: '/admin/penggajian/' + pegawaiId + '/detail/' + gajiId,
            type: 'GET',
            success: function(data) {
                modal.find('.modal-body').html(data.html);
                modal.modal('show');
            },
            error: function(xhr) {
                alert('Terjadi kesalahan saat memuat detail gaji');
            }
        });
    });

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateKalkulasiDisplay(data) {
        // JANGAN update tanggal di form periode penggajian
        // Biarkan user yang mengubah tanggal di form, kita hanya hitung ulang berdasarkan tanggal yang user input
        
        // Update periode info - gunakan nilai dari form yang sudah ada, bukan dari data response
        // Ini memastikan periode tidak berubah setelah hitung ulang
        const periodeMulai = $('#tanggal-mulai').val();
        const periodeAkhir = $('#tanggal-akhir').val();
        const periodeMulaiFormatted = formatDate(periodeMulai);
        const periodeAkhirFormatted = formatDate(periodeAkhir);
        
        $('.alert-info').html(
            '<strong>Periode:</strong> ' + periodeMulaiFormatted + ' - ' + periodeAkhirFormatted + '<br>' +
            '<strong>Hari Kerja:</strong> ' + data.number_of_days + ' hari (berdasarkan rekap yang divalidasi)'
        );

        // Update tabel rincian gaji harian
        let rincianHtml = '';
        let totalGaji = 0;
        let totalBonus = 0;

        if (data.rincian && data.rincian.length > 0) {
            data.rincian.forEach(function(rincian) {
                totalGaji += rincian.gaji;
                totalBonus += rincian.bonus;
                
                // Konversi nama hari dari bahasa Inggris ke Indonesia jika diperlukan
                let hariIndonesian = rincian.hari;
                if (typeof rincian.hari === 'string') {
                    const dayMap = {
                        'Sunday': 'Minggu',
                        'Monday': 'Senin',
                        'Tuesday': 'Selasa',
                        'Wednesday': 'Rabu',
                        'Thursday': 'Kamis',
                        'Friday': 'Jumat',
                        'Saturday': 'Sabtu'
                    };
                    hariIndonesian = dayMap[rincian.hari] || rincian.hari;
                }
                
                rincianHtml += '<tr>' +
                    '<td>' + rincian.no + '</td>' +
                    '<td>' + hariIndonesian + '</td>' +
                    '<td>' + rincian.tanggal + '</td>' +
                    '<td>' + rincian.total_donat + '</td>' +
                    '<td>Rp ' + formatNumber(rincian.gaji) + '</td>' +
                    '<td>Rp ' + formatNumber(rincian.bonus) + '</td>' +
                    '</tr>';
            });

            // Tambahkan total
            rincianHtml += '<tr class="table-info">' +
                '<th colspan="4">Total</th>' +
                '<th>Rp ' + formatNumber(totalGaji) + '</th>' +
                '<th>Rp ' + formatNumber(totalBonus) + '</th>' +
                '</tr>';
        } else {
            rincianHtml = '<tr><td colspan="6" class="text-center">Tidak ada data operasional.</td></tr>';
        }

        $('#rincian-gaji-body').html(rincianHtml);

        // Update tabel rekap gaji
        const totalKasbon = {{ $totalKasbon }};
        
        // Hitung total catatan gaji
        const totalTambahanGaji = data.total_catatan_tambahan || 0;
        const totalPenguranganGaji = data.total_catatan_pengurangan || 0;
        const gajiBersih = data.total + totalTambahanGaji - totalPenguranganGaji - totalKasbon;
        
        // Update baris-baris di tabel rekap gaji
        $('.table tbody tr').each(function() {
            const firstCell = $(this).find('td:first').text().trim();
            if (firstCell === 'Gaji Harian') {
                const totalGajiHarian = data.gaji_dasar + data.tambahan_1 + data.tambahan_2 + data.tambahan_3 + data.tambahan_4;
                $(this).find('td:last').text('Rp ' + formatNumber(totalGajiHarian));
            } else if (firstCell === 'Bonus Reguler') {
                $(this).find('td:last').text('Rp ' + formatNumber(data.bonus_reguler));
            } else if (firstCell === 'Total Gaji') {
                $(this).find('td:last').text('Rp ' + formatNumber(data.total));
            } else if (firstCell === 'Tambahan Catatan') {
                if (totalTambahanGaji > 0) {
                    $(this).find('td:last').text('Rp ' + formatNumber(totalTambahanGaji));
                } else {
                    $(this).remove();
                }
            } else if (firstCell === 'Pengurangan Catatan') {
                if (totalPenguranganGaji > 0) {
                    $(this).find('td:last').text('Rp ' + formatNumber(totalPenguranganGaji));
                } else {
                    $(this).remove();
                }
            } else if (firstCell === 'Gaji Bersih') {
                $(this).find('td:last').text('Rp ' + formatNumber(gajiBersih));
            }
        });

        // Update gaji total di bagian bawah
        $('#gaji-total').text('Rp ' + formatNumber(gajiBersih));

        // Enable/disable tombol validasi
        if (gajiBersih <= 0) {
            $('#validate-gaji').prop('disabled', true);
        } else {
            $('#validate-gaji').prop('disabled', false);
        }
    }

    // Format ribuan untuk input jumlah catatan gaji
    $(document).on('input', '#jumlah-catatan', function() {
        let value = $(this).val().replace(/\./g, '');
        if (value.length >= 3) {
            value = parseInt(value).toLocaleString('id-ID');
            $(this).val(value);
        }
    });

    // Handle tambah catatan gaji
    $('#simpan-catatan-gaji').click(function() {
        const pegawaiId = {{ $pegawai->id }};
        const jenis = $('input[name="jenis"]:checked').val();
        let jumlah = $('#jumlah-catatan').val().replace(/\./g, '');
        const catatan = $('#catatan-keterangan').val();

        if (!jenis || !jumlah) {
            alert('Mohon isi jenis dan jumlah catatan.');
            return;
        }

        jumlah = parseInt(jumlah);

        $.ajax({
            url: '/admin/penggajian/' + pegawaiId + '/tambah-catatan-gaji',
            type: 'POST',
            data: {
                jenis: jenis,
                jumlah: jumlah,
                catatan: catatan,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                alert('Catatan gaji berhasil ditambahkan!');
                $('#tambahCatatanGajiModal').modal('hide');
                $('#form-tambah-catatan-gaji')[0].reset();
                location.reload();
            },
            error: function(xhr) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    alert('Error: ' + (response.error || 'Terjadi kesalahan'));
                } catch (e) {
                    alert('Terjadi kesalahan saat menambahkan catatan gaji');
                }
            }
        });
    });

    // Handle hapus catatan gaji
    $(document).on('click', '.hapus-catatan-gaji', function() {
        const pegawaiId = {{ $pegawai->id }};
        const index = $(this).data('index');

        if (confirm('Apakah Anda yakin ingin menghapus catatan ini?')) {
            $.ajax({
                url: '/admin/penggajian/' + pegawaiId + '/hapus-catatan-gaji',
                type: 'POST',
                data: {
                    index: index,
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    alert('Catatan gaji berhasil dihapus!');
                    location.reload();
                },
                error: function(xhr) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        alert('Error: ' + (response.error || 'Terjadi kesalahan'));
                    } catch (e) {
                        alert('Terjadi kesalahan saat menghapus catatan gaji');
                    }
                }
            });
        }
    });

    function formatDate(dateString) {
        if (!dateString) return '';
        // Handle format YYYY-MM-DD (dari input date)
        if (dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
            const [year, month, day] = dateString.split('-');
            const date = new Date(year, month - 1, day);
            return date.toLocaleDateString('id-ID');
        }
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        return date.toLocaleDateString('id-ID');
    }

    function formatDateForInput(dateString) {
        // Handle jika sudah dalam format YYYY-MM-DD
        if (dateString && dateString.match(/^\d{4}-\d{2}-\d{2}$/)) {
            return dateString;
        }
        // Handle jika dalam format lain
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return '';
        }
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function getDayNameIndonesian(dayOfWeek) {
        const days = {
            0: 'Minggu',
            1: 'Senin',
            2: 'Selasa',
            3: 'Rabu',
            4: 'Kamis',
            5: 'Jumat',
            6: 'Sabtu'
        };
        return days[dayOfWeek] || 'Tidak Diketahui';
    }
});
</script>
@endsection

@section('back-button')
<a href="javascript:history.back()" class="btn btn-outline-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0"><i class="bi bi-person-circle me-2"></i>Detail Penggajian - {{ $pegawai->nama }}</h5>
                </div>

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

                <!-- Info Pegawai -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Informasi Pegawai</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Nama:</strong></td>
                                        <td>{{ $pegawai->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Outlet:</strong></td>
                                        <td>{{ $pegawai->outlet->nama ?? 'Belum ditugaskan' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Target Penjualan</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Target 1:</strong></td>
                                        <td>{{ $pegawai->target_1 }} donat</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tambahan Gaji 1:</strong></td>
                                        <td>Rp {{ number_format($pegawai->tambahan_gaji_1, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Target 2:</strong></td>
                                        <td>{{ $pegawai->target_2 }} donat</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tambahan Gaji 2:</strong></td>
                                        <td>Rp {{ number_format($pegawai->tambahan_gaji_2, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Target 3:</strong></td>
                                        <td>{{ $pegawai->target_3 }} donat</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tambahan Gaji 3:</strong></td>
                                        <td>Rp {{ number_format($pegawai->tambahan_gaji_3, 0, ',', '.') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Target 4:</strong></td>
                                        <td>{{ $pegawai->target_4 }} donat</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tambahan Gaji 4:</strong></td>
                                        <td>Rp {{ number_format($pegawai->tambahan_gaji_4, 0, ',', '.') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Periode Penggajian -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Atur Periode Penggajian</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="tanggal-mulai" class="form-label">Tanggal Mulai</label>
                                        <input type="date" class="form-control" id="tanggal-mulai" value="{{ $kalkulasiGaji['start_date']->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="tanggal-akhir" class="form-label">Tanggal Akhir</label>
                                        <input type="date" class="form-control" id="tanggal-akhir" value="{{ $kalkulasiGaji['end_date']->format('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="periode-keterangan" class="form-label">Keterangan Periode</label>
                                        <input type="text" class="form-control" id="periode-keterangan" placeholder="Contoh: Gaji Mingguan, Gaji 2 Minggu">
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-12">
                                        <button type="button" class="btn btn-info" id="hitung-ulang">
                                            <i class="bi bi-calculator"></i> Hitung Ulang Gaji
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Kalkulasi Gaji</h6>
                                <div class="row mb-3">
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <strong>Periode:</strong> {{ $kalkulasiGaji['start_date']->format('d/m/Y') }} - {{ $kalkulasiGaji['end_date']->format('d/m/Y') }}<br>
                                            <strong>Hari Kerja:</strong> {{ $kalkulasiGaji['number_of_days'] }} hari (berdasarkan rekap yang divalidasi)
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="gaji-breakdown">
                                            <h6>Rincian Gaji Harian</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped" id="rincian-gaji-table">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Hari</th>
                                                            <th>Tanggal</th>
                                                            <th>Donat</th>
                                                            <th>Gaji</th>
                                                            <th>Bonus</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="rincian-gaji-body">
                                                        @php
                                                            $totalGaji = 0;
                                                            $totalBonus = 0;
                                                        @endphp
                                                        @forelse($kalkulasiGaji['rincian'] as $rincian)
                                                            @php
                                                                $totalGaji += $rincian['gaji'];
                                                                $totalBonus += $rincian['bonus'];
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $rincian['no'] }}</td>
                                                                <td>{{ $rincian['hari'] }}</td>
                                                                <td>{{ $rincian['tanggal'] }}</td>
                                                                <td>{{ $rincian['total_donat'] }}</td>
                                                                <td>Rp {{ number_format($rincian['gaji'], 0, ',', '.') }}</td>
                                                                <td>Rp {{ number_format($rincian['bonus'], 0, ',', '.') }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center">Tidak ada data operasional.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    @if(count($kalkulasiGaji['rincian']) > 0)
                                                    <tfoot>
                                                        <tr class="table-info">
                                                            <th colspan="4">Total</th>
                                                            <th>Rp {{ number_format($totalGaji, 0, ',', '.') }}</th>
                                                            <th>Rp {{ number_format($totalBonus, 0, ',', '.') }}</th>
                                                        </tr>
                                                    </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Kasbon Pegawai yang Belum Dibayar -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="gaji-breakdown">
                                            <h6>Kasbon Pegawai yang Belum Dibayar</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Tanggal</th>
                                                            <th>Nominal</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($kasbonBelumDibayar as $index => $kasbon)
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>{{ \Carbon\Carbon::parse($kasbon->tanggal)->format('d/m/Y') }}</td>
                                                                <td>Rp {{ number_format($kasbon->nominal, 0, ',', '.') }}</td>
                                                                <td>{{ $kasbon->keterangan }}</td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center">Tidak ada kasbon yang belum dibayar.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="2">Total Kasbon</th>
                                                            <th>Rp {{ number_format($totalKasbon, 0, ',', '.') }}</th>
                                                            <th></th>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Catatan Gaji -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="gaji-breakdown">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h6>Catatan Gaji</h6>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#tambahCatatanGajiModal">
                                                    <i class="bi bi-plus-circle"></i> Tambah Catatan
                                                </button>
                                            </div>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>No</th>
                                                            <th>Jenis</th>
                                                            <th>Jumlah</th>
                                                            <th>Catatan</th>
                                                            <th>Aksi</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="catatan-gaji-body">
                                                        @php
                                                            $totalTambahanGaji = 0;
                                                            $totalPenguranganGaji = 0;
                                                        @endphp
                                                        @forelse($tempCatatanGaji ?? [] as $index => $catatan)
                                                            @php
                                                                if ($catatan['jenis'] === 'tambahan') {
                                                                    $totalTambahanGaji += $catatan['jumlah'];
                                                                } else {
                                                                    $totalPenguranganGaji += $catatan['jumlah'];
                                                                }
                                                            @endphp
                                                            <tr>
                                                                <td>{{ $index + 1 }}</td>
                                                                <td>
                                                                    @if ($catatan['jenis'] === 'tambahan')
                                                                        <span class="badge bg-success">
                                                                            <i class="bi bi-plus-circle me-1"></i>Tambahan
                                                                        </span>
                                                                    @else
                                                                        <span class="badge bg-danger">
                                                                            <i class="bi bi-dash-circle me-1"></i>Pengurangan
                                                                        </span>
                                                                    @endif
                                                                </td>
                                                                <td class="fw-semibold {{ $catatan['jenis'] === 'tambahan' ? 'text-success' : 'text-danger' }}">
                                                                    Rp {{ number_format($catatan['jumlah'], 0, ',', '.') }}
                                                                </td>
                                                                <td>{{ $catatan['catatan'] ?? '-' }}</td>
                                                                <td>
                                                                    <button class="btn btn-danger btn-sm hapus-catatan-gaji" data-index="{{ $index }}">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="5" class="text-center text-muted">Belum ada catatan gaji.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                    @if(isset($tempCatatanGaji) && count($tempCatatanGaji) > 0)
                                                    <tfoot>
                                                        <tr>
                                                            <th colspan="2">Total Tambahan</th>
                                                            <th class="text-success">Rp {{ number_format($totalTambahanGaji, 0, ',', '.') }}</th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                        <tr>
                                                            <th colspan="2">Total Pengurangan</th>
                                                            <th class="text-danger">Rp {{ number_format($totalPenguranganGaji, 0, ',', '.') }}</th>
                                                            <th colspan="2"></th>
                                                        </tr>
                                                    </tfoot>
                                                    @endif
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabel Rekap Gaji -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="gaji-breakdown">
                                            <h6>Rekap Gaji</h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-striped rekap-gaji-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Keterangan</th>
                                                            <th>Nominal</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>Gaji Harian</td>
                                                            <td>Rp {{ number_format($kalkulasiGaji['gaji_dasar'] + $kalkulasiGaji['tambahan_1'] + $kalkulasiGaji['tambahan_2'] + $kalkulasiGaji['tambahan_3'] + $kalkulasiGaji['tambahan_4'], 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Bonus Reguler</td>
                                                            <td>Rp {{ number_format($kalkulasiGaji['bonus_reguler'], 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="table-info">
                                                            <td><strong>Total Gaji</strong></td>
                                                            <td><strong>Rp {{ number_format($kalkulasiGaji['total'], 0, ',', '.') }}</strong></td>
                                                        </tr>
                                                        @php
                                                            $totalTambahanGaji = isset($tempCatatanGaji) ? collect($tempCatatanGaji)->where('jenis', 'tambahan')->sum('jumlah') : 0;
                                                            $totalPenguranganGaji = isset($tempCatatanGaji) ? collect($tempCatatanGaji)->where('jenis', 'pengurangan')->sum('jumlah') : 0;
                                                        @endphp
                                                        @if($totalTambahanGaji > 0)
                                                        <tr>
                                                            <td class="text-success">Tambahan Catatan</td>
                                                            <td class="text-success">Rp {{ number_format($totalTambahanGaji, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        @if($totalPenguranganGaji > 0)
                                                        <tr>
                                                            <td class="text-danger">Pengurangan Catatan</td>
                                                            <td class="text-danger">Rp {{ number_format($totalPenguranganGaji, 0, ',', '.') }}</td>
                                                        </tr>
                                                        @endif
                                                        <tr>
                                                            <td>Kasbon</td>
                                                            <td>Rp {{ number_format($totalKasbon, 0, ',', '.') }}</td>
                                                        </tr>
                                                        <tr class="table-info">
                                                            <td><strong>Gaji Bersih</strong></td>
                                                            <td><strong>Rp {{ number_format($kalkulasiGaji['total'] + $totalTambahanGaji - $totalPenguranganGaji - $totalKasbon, 0, ',', '.') }}</strong></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $totalTambahanGajiCalc = isset($tempCatatanGaji) ? collect($tempCatatanGaji)->where('jenis', 'tambahan')->sum('jumlah') : 0;
                                    $totalPenguranganGajiCalc = isset($tempCatatanGaji) ? collect($tempCatatanGaji)->where('jenis', 'pengurangan')->sum('jumlah') : 0;
                                    $gajiBersih = $kalkulasiGaji['total'] + $totalTambahanGajiCalc - $totalPenguranganGajiCalc - $totalKasbon;
                                @endphp
                                <div class="row mt-4">
                                    <div class="col-md-4 offset-md-8">
                                        <div class="gaji-total">
                                            <h4>Gaji Bersih</h4>
                                            <h2 id="gaji-total">Rp {{ number_format($gajiBersih, 0, ',', '.') }}</h2>
                                            <button class="btn btn-success mt-3" id="validate-gaji" {{ $gajiBersih <= 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-check-circle"></i> Validasi Gaji
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Histori Gaji -->
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="card-title">Histori Gaji</h6>
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Periode</th>
                                                <th>Tanggal Gaji</th>
                                                <th>Gaji Total</th>
                                                <th>Status</th>
                                                <th>Dibuat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($gajiHistori as $gaji)
                                                <tr>
                                                    <td>
                                                        @if($gaji->periode_mulai && $gaji->periode_akhir)
                                                            {{ \Carbon\Carbon::parse($gaji->periode_mulai)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($gaji->periode_akhir)->format('d/m/Y') }}
                                                            @if($gaji->periode_keterangan)
                                                                <br><small class="text-muted">{{ $gaji->periode_keterangan }}</small>
                                                            @endif
                                                        @else
                                                            {{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d F Y') }}
                                                        @endif
                                                    </td>
                                                    <td>{{ \Carbon\Carbon::parse($gaji->tanggal_gaji)->format('d F Y') }}</td>
                                                    <td>Rp {{ number_format($gaji->gaji_total, 0, ',', '.') }}</td>
                                                    <td>
                                                        <span class="status-{{ $gaji->status }}">
                                                            {{ $gaji->status === 'paid' ? 'Dibayar' : 'Belum Dibayar' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $gaji->created_at->format('d/m/Y H:i') }}</td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm btn-detail-gaji" data-gaji-id="{{ $gaji->id }}">
                                                            Detail
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="text-center">Belum ada histori gaji.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination -->
                                @if ($gajiHistori->hasPages())
                                <div class="pagination-wrapper">
                                    <nav aria-label="Navigasi halaman histori gaji">
                                        <ul class="pagination">
                                            <!-- Previous Page Link -->
                                            @if ($gajiHistori->onFirstPage())
                                                <li class="page-item disabled">
                                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                                                </li>
                                            @else
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $gajiHistori->previousPageUrl() }}" rel="prev">Previous</a>
                                                </li>
                                            @endif

                                            <!-- Pagination Elements -->
                                            @foreach ($gajiHistori->getUrlRange(1, $gajiHistori->lastPage()) as $page => $url)
                                                @if ($page == $gajiHistori->currentPage())
                                                    <li class="page-item active" aria-current="page">
                                                        <a class="page-link" href="#">{{ $page }}</a>
                                                    </li>
                                                @else
                                                    <li class="page-item">
                                                        <a class="page-link" href="{{ $url }}">{{ $page }}</a>
                                                    </li>
                                                @endif
                                            @endforeach

                                            <!-- Next Page Link -->
                                            @if ($gajiHistori->hasMorePages())
                                                <li class="page-item">
                                                    <a class="page-link" href="{{ $gajiHistori->nextPageUrl() }}" rel="next">Next</a>
                                                </li>
                                            @else
                                                <li class="page-item disabled">
                                                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
                                                </li>
                                            @endif
                                        </ul>
                                    </nav>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal Tambah Catatan Gaji -->
<div class="modal fade" id="tambahCatatanGajiModal" tabindex="-1" aria-labelledby="tambahCatatanGajiModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="tambahCatatanGajiModalLabel">Tambah Catatan Gaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="form-tambah-catatan-gaji">
                    <div class="mb-3">
                        <label class="form-label d-block mb-2">Jenis</label>
                        <div class="btn-group w-100" role="group" aria-label="Jenis Catatan Gaji">
                            <input type="radio" class="btn-check" name="jenis" id="jenis-tambahan" value="tambahan" autocomplete="off" checked required>
                            <label class="btn btn-outline-success" for="jenis-tambahan">
                                <i class="bi bi-plus-circle me-1"></i>Tambahan
                            </label>
                            <input type="radio" class="btn-check" name="jenis" id="jenis-pengurangan" value="pengurangan" autocomplete="off">
                            <label class="btn btn-outline-danger" for="jenis-pengurangan">
                                <i class="bi bi-dash-circle me-1"></i>Pengurangan
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah-catatan" class="form-label">Jumlah (Rp)</label>
                        <input type="text" class="form-control thousand-input" id="jumlah-catatan" name="jumlah" inputmode="numeric" autocomplete="off" required>
                    </div>
                    <div class="mb-3">
                        <label for="catatan-keterangan" class="form-label">Keterangan</label>
                        <textarea class="form-control" id="catatan-keterangan" name="catatan" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" id="simpan-catatan-gaji">Simpan</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailGajiModal" tabindex="-1" aria-labelledby="detailGajiModalLabel" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailGajiModalLabel">Detail Gaji</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Konten modal akan dimuat via AJAX -->
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Memuat detail gaji...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
