@extends('layouts.app')

@section('title', 'Pengaturan AIPOS')

@section('content')
<div class="ui-page">
    <a href="{{ route('admin.dashboard') }}" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <header class="ui-header">
        <div>
            <h1>Pengaturan AIPOS</h1>
            <p>Kredensial bersama untuk pegawai melihat transaksi QRIS</p>
        </div>
    </header>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="ui-panel">
        <form action="{{ route('admin.aipos.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="aipos_url" class="form-label">URL AIPOS</label>
                <input type="url"
                       class="form-control"
                       id="aipos_url"
                       name="aipos_url"
                       value="{{ old('aipos_url', $settings['aipos_url']) }}"
                       placeholder="https://www.aiposystem.com/my/dashboard"
                       required>
            </div>

            <div class="mb-3">
                <label for="aipos_email" class="form-label">Email / Username</label>
                <input type="text"
                       class="form-control"
                       id="aipos_email"
                       name="aipos_email"
                       value="{{ old('aipos_email', $settings['aipos_email']) }}"
                       autocomplete="off"
                       required>
            </div>

            <div class="mb-3">
                <label for="aipos_password" class="form-label">Password</label>
                <div class="input-group">
                    <input type="password"
                           class="form-control"
                           id="aipos_password"
                           name="aipos_password"
                           value="{{ old('aipos_password', $settings['aipos_password']) }}"
                           autocomplete="new-password"
                           required>
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" aria-label="Tampilkan password">
                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
                <small class="text-muted">Password ini akan terlihat oleh semua pegawai di halaman Akses AIPOS.</small>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-lg me-1"></i> Simpan
            </button>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        var input = document.getElementById('aipos_password');
        var icon = document.getElementById('togglePasswordIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    });
</script>
@endsection
