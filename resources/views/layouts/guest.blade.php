@extends('layouts.base')

@section('body')
    <div class="auth-shell">
        <div class="auth-card">
            <div class="auth-brand">
                <img src="{{ asset('assets/smealogo.png') }}" alt="Logo SMK Negeri 1 Adiwerna" class="brand-logo">
                <div class="brand-text">
                    <h1 class="brand-title">Pemilos</h1>
                    <p class="brand-subtitle">@yield('tagline', 'Sistem Pemilihan OSIS')</p>
                </div>
            </div>
            @include('shared.flash')
            @yield('content')
        </div>
        @include('shared.footer')
    </div>
@endsection
