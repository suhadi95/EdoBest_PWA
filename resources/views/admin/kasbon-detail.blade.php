@extends('layouts.app')

@section('title', 'Detail Kasbon Pegawai')

@section('content')
<div class="container">
    <h3>Kasbon Pegawai: {{ $pegawai->nama }}</h3>

    <h4>Pengajuan Kasbon</h4>
    @if($pengajuanKasbon->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Nominal</th>
                <th>Keterangan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuanKasbon as $kasbon)
            <tr>
                <td>{{ $kasbon->tanggal }}</td>
                <td>Rp {{ number_format($kasbon->nominal) }}</td>
                <td>{{ $kasbon->keterangan }}</td>
                <td>
                    <button class="btn btn-success btn-sm approve-btn" data-id="{{ $kasbon->id }}">Setujui</button>
                    <button class="btn btn-danger btn-sm reject-btn" data-id="{{ $kasbon->id }}">Tolak</button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Tidak ada pengajuan kasbon.</p>
    @endif

    <h4>Histori Kasbon</h4>
    @if($historiKasbon->count() > 0)
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Nominal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Pengajuan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($historiKasbon as $kasbon)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($kasbon->tanggal)->format('d/m/Y') }}</td>
                    <td>Rp {{ number_format($kasbon->nominal, 0, ',', '.') }}</td>
                    <td>{{ $kasbon->keterangan }}</td>
                    <td>
                        @if($kasbon->status == 'pending')
                            <span class="badge bg-warning">Pending</span>
                        @elseif($kasbon->status == 'approved')
                            <span class="badge bg-success">Disetujui</span>
                        @elseif($kasbon->status == 'rejected')
                            <span class="badge bg-danger">Ditolak</span>
                        @endif
                    </td>
                    <td>
                        @if($kasbon->status_pembayaran == 'lunas')
                            <span class="badge bg-success">Lunas</span>
                        @else
                            <span class="badge bg-warning">Belum Dibayar</span>
                        @endif
                    </td>
                    <td>{{ $kasbon->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p class="text-muted">Belum ada histori kasbon.</p>
    @endif

    <a href="{{ route('admin.kasbon.index') }}" class="btn btn-secondary">Kembali</a>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('.approve-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/kasbon/' + id + '/approve',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                alert(data.success);
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal menyetujui kasbon');
            }
        });
    });

    $('.reject-btn').click(function() {
        var id = $(this).data('id');
        $.ajax({
            url: '/admin/kasbon/' + id + '/reject',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                alert(data.success);
                location.reload();
            },
            error: function(xhr) {
                alert('Gagal menolak kasbon');
            }
        });
    });
});
</script>
@endsection
