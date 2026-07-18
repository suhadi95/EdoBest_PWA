@extends('layouts.app')

@section('title', 'Kasbon Admin')

@section('back-button')
<a class="ui-back" href="javascript:history.back()"><i class="bi bi-arrow-left"></i> Kembali</a>
@endsection

@section('content')
<div class="ui-page">
    <header class="ui-header">
        <div>
            <h1>Kelola Kasbon</h1>
            <p>Pilih pegawai</p>
        </div>
    </header>

    <section class="ui-section">
        <h2 class="ui-section__title">Daftar Pegawai</h2>

        @if ($pegawai->isNotEmpty())
            <div class="ui-menu">
                @foreach ($pegawai as $p)
                    <a href="{{ route('admin.kasbon.show', $p->id) }}" class="ui-menu__item">
                        <div class="ui-menu__icon ui-icon--sky"><i class="bi bi-wallet2"></i></div>
                        <div class="ui-menu__text">
                            <strong>{{ $p->nama }}</strong>
                            <span>{{ $p->username }}</span>
                        </div>
                        <i class="bi bi-chevron-right ui-menu__chevron"></i>
                    </a>
                @endforeach
            </div>
        @else
            <div class="ui-empty">
                <i class="bi bi-people"></i>
                <p>Belum ada pegawai terdaftar.</p>
            </div>
        @endif
    </section>
</div>
@endsection
