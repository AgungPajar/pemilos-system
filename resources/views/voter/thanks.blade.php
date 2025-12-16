@extends('layouts.guest')

@section('title', 'Terima Kasih')
@section('tagline', 'Suaramu sudah kami catat')

@section('content')
    <div class="thanks-card">
        <h2>Terima kasih!</h2>
        <p>Suaramu untuk <strong>{{ $paslonName }}</strong> telah direkam dengan aman.</p>
        <p class="muted">Silakan kembali ke halaman login jika ingin membantu teman lain untuk memilih dengan token berbeda.</p>
        <a href="{{ route('voter.login') }}" class="primary-button mt">Kembali ke Login</a>

        <div class="credits" style="margin-top: 32px; text-align: center;">
            <p class="muted">Website ini dibuat oleh <a href="https://www.instagram.com/jarss_pajar?igsh=YmJlcWhqc204Zzgw" target="_blank" rel="noopener noreferrer" style="color: var(--accent); text-decoration: none;">@jarss_pajar</a></p>
            <p style="margin-top:10px;color:var(--text-muted);">Jangan lupa di follow yaa guyss</p>
        </div>
    </div>
@endsection
