@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<div class="row">
    <div class="col-12 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Selamat Datang di EdoBest</h5>
                <p class="card-text">Ini adalah halaman beranda untuk sistem manajemen outlet dan pegawai.</p>
                <a href="{{ url('/login') }}" class="btn btn-primary">Login Sekarang</a>
            </div>
        </div>
    </div>
</div>
@endsection