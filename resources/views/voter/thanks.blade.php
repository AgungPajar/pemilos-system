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
            <p class="muted">Created by <a href="https://www.gncs.dev/" target="_blank" rel="noopener noreferrer" style="color: var(--accent); text-decoration: none;">GnC Team</a></p>
            <img src="{{ asset('logocreate.png') }}" alt="GnC Team Logo" style="width: 120px; height: auto; margin-top: 16px; opacity: 0.8;">
        </div>
    </div>
@endsection
