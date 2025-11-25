@extends('layouts.guest')

@section('title', 'Terima Kasih')
@section('tagline', 'Suaramu sudah kami catat')

@section('content')
    <div class="thanks-card">
        <h2>Terima kasih!</h2>
        <p>Suaramu untuk <strong>{{ $paslonName }}</strong> telah direkam dengan aman.</p>
        <p class="muted">Silakan kembali ke halaman login jika ingin membantu teman lain untuk memilih dengan token berbeda.</p>
        <a href="{{ route('voter.login') }}" class="primary-button mt">Kembali ke Login</a>
        <a href="https://gncs.dev" target="_blank" rel="noopener noreferrer" class="primary-button mt">Lihat Tentang Kami</a>
        <p class="muted mt">Created by GNCS x PPLG</p>
    </div>
@endsection
