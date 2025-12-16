@extends('layouts.base')

@section('body')
    <div class="voter-shell">
        <header class="voter-header">
            <div class="header-logos">
                <div class="header-logo">
                    <img src="{{ asset('assets/logobosdugar.png') }}" alt="OSIS Logo">
                </div>
                <div class="header-logo">
                    <img src="{{ asset('assets/logostm.png') }}" alt="STM Logo">
                </div>
            </div>
            <div class="header-center">
                <h1 class="brand-title">Pemilos Bosdugar</h1>
                <p class="brand-subtitle">Satu suara menentukan masa depan</p>
            </div>
        </header>
        <main class="voter-main">
            @include('shared.flash')
            @yield('content')
        </main>
        @include('shared.footer')
    </div>
@endsection
