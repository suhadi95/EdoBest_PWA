@extends('layouts.app')

@section('title', 'Akses AIPOS')

@section('css')
<style>
    .aipos-cred {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.85rem 0;
        border-bottom: 1px solid #f0f1f4;
    }
    .aipos-cred:last-child { border-bottom: none; padding-bottom: 0; }
    .aipos-cred:first-child { padding-top: 0; }
    .aipos-cred__body { flex: 1; min-width: 0; }
    .aipos-cred__label {
        display: block;
        font-size: 0.72rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--muted);
        margin-bottom: 0.2rem;
    }
    .aipos-cred__value {
        font-size: 0.95rem;
        font-weight: 600;
        word-break: break-all;
        color: var(--dark-color);
    }
    .aipos-steps {
        margin: 0;
        padding-left: 1.15rem;
        color: var(--secondary-color);
        font-size: 0.875rem;
        line-height: 1.6;
    }
</style>
@endsection

@section('content')
<div class="ui-page">
    <a href="{{ route('pegawai.dashboard') }}" class="ui-back"><i class="bi bi-arrow-left"></i> Kembali</a>

    <header class="ui-header">
        <div>
            <h1>Akses AIPOS</h1>
            <p>Lihat riwayat transaksi QRIS di situs AIPOS</p>
        </div>
    </header>

    @if (!$configured)
        <div class="ui-empty">
            <i class="bi bi-exclamation-circle"></i>
            <p class="mb-0">Belum dikonfigurasi. Hubungi admin untuk mengisi email &amp; password AIPOS.</p>
        </div>
    @else
        <a href="{{ $settings['aipos_url'] }}"
           class="ui-primary"
           target="_blank"
           rel="noopener noreferrer">
            <div class="ui-primary__icon"><i class="bi bi-box-arrow-up-right"></i></div>
            <div class="ui-primary__body">
                <strong>Buka AIPOS</strong>
                <span>Login di situs AIPOS untuk melihat transaksi</span>
            </div>
            <i class="bi bi-arrow-right"></i>
        </a>

        <section class="ui-section">
            <h2 class="ui-section__title">Kredensial login</h2>
            <div class="ui-panel">
                <div class="aipos-cred">
                    <div class="aipos-cred__body">
                        <span class="aipos-cred__label">Email / Username</span>
                        <div class="aipos-cred__value" id="aiposEmail">{{ $settings['aipos_email'] }}</div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-copy="aiposEmail">
                        <i class="bi bi-clipboard me-1"></i>Salin
                    </button>
                </div>
                <div class="aipos-cred">
                    <div class="aipos-cred__body">
                        <span class="aipos-cred__label">Password</span>
                        <div class="aipos-cred__value">
                            <span id="aiposPasswordMasked">••••••••</span>
                            <span id="aiposPassword" class="d-none">{{ $settings['aipos_password'] }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-secondary me-1" id="toggleAiposPassword">
                        <i class="bi bi-eye" id="toggleAiposPasswordIcon"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-copy="aiposPassword">
                        <i class="bi bi-clipboard me-1"></i>Salin
                    </button>
                </div>
            </div>
        </section>

        <section class="ui-section">
            <h2 class="ui-section__title">Cara pakai</h2>
            <div class="ui-panel">
                <ol class="aipos-steps">
                    <li>Salin email dan password di atas.</li>
                    <li>Tekan <strong>Buka AIPOS</strong>.</li>
                    <li>Tempel kredensial di halaman login AIPOS.</li>
                    <li>Buka menu transaksi untuk melihat riwayat QRIS.</li>
                </ol>
            </div>
        </section>
    @endif
</div>
@endsection

@section('js')
<script>
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = btn.getAttribute('data-copy');
            var el = document.getElementById(id);
            if (!el) return;
            var text = el.textContent.trim();
            navigator.clipboard.writeText(text).then(function () {
                var original = btn.innerHTML;
                btn.innerHTML = '<i class="bi bi-check2 me-1"></i>Disalin';
                setTimeout(function () { btn.innerHTML = original; }, 1500);
            }).catch(function () {
                alert('Gagal menyalin. Salin manual: ' + text);
            });
        });
    });

    var toggleBtn = document.getElementById('toggleAiposPassword');
    if (toggleBtn) {
        toggleBtn.addEventListener('click', function () {
            var masked = document.getElementById('aiposPasswordMasked');
            var real = document.getElementById('aiposPassword');
            var icon = document.getElementById('toggleAiposPasswordIcon');
            if (real.classList.contains('d-none')) {
                real.classList.remove('d-none');
                masked.classList.add('d-none');
                icon.className = 'bi bi-eye-slash';
            } else {
                real.classList.add('d-none');
                masked.classList.remove('d-none');
                icon.className = 'bi bi-eye';
            }
        });
    }
</script>
@endsection
