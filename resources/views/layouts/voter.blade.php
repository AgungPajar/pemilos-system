@extends('layouts.base')

@section('body')
    <div class="voter-shell">
        <header class="voter-header">
            <div class="header-left">
                <div class="brand-circle">P</div>
                <div>
                    <h1 class="brand-title">Pemilos</h1>
                    <p class="brand-subtitle">Satu suara menentukan masa depan OSIS</p>
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
    </div>
@endsection
