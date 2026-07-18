@extends('layouts.app')

@section('title', 'Detail Kasbon Pegawai')

@section('back-button')
<a class="ui-back" href="{{ route('admin.kasbon.index') }}"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page ui-page--wide">
    <header class="ui-header">
        <div>
            <h1>Kasbon Pegawai</h1>
            <p>{{ $pegawai->nama }}</p>
        </div>
    </header>

    <section class="ui-section">
        <h2 class="ui-section__title">Pengajuan Kasbon</h2>
        @if($pengajuanKasbon->count() > 0)
            <div class="ui-panel" style="padding:0; overflow:hidden;">
                <div class="table-responsive" style="border:none; border-radius:0;">
                    <table class="table table-hover mb-0">
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
                                    <button class="btn btn-outline-primary btn-sm approve-btn" data-id="{{ $kasbon->id }}">Setujui</button>
                                    <button class="btn btn-outline-danger btn-sm reject-btn" data-id="{{ $kasbon->id }}">Tolak</button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="ui-empty">
                <i class="bi bi-inbox"></i>
                <p>Tidak ada pengajuan kasbon.</p>
            </div>
        @endif
    </section>

    <section class="ui-section">
        <h2 class="ui-section__title">Histori Kasbon</h2>
        @if($historiKasbon->count() > 0)
            <div class="ui-panel" style="padding:0; overflow:hidden;">
                <div class="table-responsive" style="border:none; border-radius:0;">
                    <table class="table table-striped mb-0">
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
                                        <span class="ui-chip ui-chip--amber">Pending</span>
                                    @elseif($kasbon->status == 'approved')
                                        <span class="ui-chip ui-chip--green">Disetujui</span>
                                    @elseif($kasbon->status == 'rejected')
                                        <span class="ui-chip ui-chip--rose">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    @if($kasbon->status_pembayaran == 'lunas')
                                        <span class="ui-chip ui-chip--green">Lunas</span>
                                    @else
                                        <span class="ui-chip ui-chip--amber">Belum Dibayar</span>
                                    @endif
                                </td>
                                <td>{{ $kasbon->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <p class="text-muted mb-0">Belum ada histori kasbon.</p>
        @endif
    </section>
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
