@extends('layouts.base')

@section('body')
    <div class="voter-shell">
        <header class="voter-header">
            <div class="header-left">
                <div class="brand-circle">
                    <img src="{{ asset('assets/logobosdugar.png') }}" alt="SME Logo">
                </div>
                <div>
                    <h1 class="brand-title" style="text-align: center;">Pemilos</h1>
                    <p class="brand-subtitle" style="text-align: center;">Satu suara menentukan masa depan</p>
                </div>
            </div>
            <div class="header-right">
                <form method="POST" action="{{ route('voter.logout') }}">
                    @csrf
                    <button type="submit" class="ghost-button">Keluar</button>
                </form>
            </div>
        </header>
        <main class="voter-main">
            @include('shared.flash')
            @yield('content')
        </main>
        @include('shared.footer')
    </div>
@endsection
