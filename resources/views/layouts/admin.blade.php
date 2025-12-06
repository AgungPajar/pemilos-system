@extends('layouts.base')

@php
    $navItems = [
        ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'pattern' => 'admin.dashboard'],
        ['label' => 'Paslon', 'route' => 'admin.paslon.index', 'pattern' => 'admin.paslon.*'],
        ['label' => 'Token', 'route' => 'admin.tokens.index', 'pattern' => 'admin.tokens.*'],
        ['label' => 'Siswa PKL', 'route' => 'admin.pkl-students.index', 'pattern' => 'admin.pkl-students.*'],
    ];
@endphp

@section('body')
    <div class="admin-shell" data-admin-shell>
        <aside class="admin-sidebar" data-sidebar>
            <button type="button" class="sidebar-close" data-sidebar-close>
                <span aria-hidden="true">&times;</span>
                <span class="sr-only">Tutup menu</span>
            </button>
            <div class="sidebar-head">
                <img src="{{ asset('assets/logobosdugar.png') }}" alt="Logo SMK Negeri 1 Adiwerna" class="sidebar-logo">
                <div>
                    <h1 class="brand-title">Pemilos</h1>
                    <p class="brand-subtitle">Panel Admin</p>
                </div>
            </div>
            <nav class="sidebar-nav">
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="nav-link {{ request()->routeIs($item['pattern'] ?? $item['route']) ? 'is-active' : '' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}" class="sidebar-logout">
                @csrf
                <button type="submit" class="nav-link logout-button">Keluar</button>
            </form>
        </aside>
        <div class="sidebar-overlay" data-sidebar-close></div>
        <main class="admin-main">
            <header class="admin-header">
                <div>
                    <button type="button" class="menu-toggle" data-sidebar-toggle>
                        <span class="sr-only">Buka menu</span>
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <div>
                        <h2 class="page-title">@yield('title', 'Dashboard')</h2>
                        <p class="page-subtitle">@yield('subtitle', 'Ringkasan status pemilos hari ini')</p>
                    </div>
                </div>
                <div class="header-meta">
                    <span class="meta-badge">{{ now()->format('d M Y') }}</span>
                </div>
            </header>
            <section class="admin-content">
                @include('shared.flash')
                @yield('content')
            </section>
        </main>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            var shell = document.querySelector('[data-admin-shell]');
            if (!shell) {
                return;
            }

            var toggleButtons = document.querySelectorAll('[data-sidebar-toggle]');
            var closeButtons = document.querySelectorAll('[data-sidebar-close]');

            toggleButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    shell.classList.toggle('sidebar-open');
                });
            });

            closeButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    shell.classList.remove('sidebar-open');
                });
            });

            document.querySelectorAll('.admin-sidebar .nav-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 1024) {
                        shell.classList.remove('sidebar-open');
                    }
                });
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 1024) {
                    shell.classList.remove('sidebar-open');
                }
            });
        })();
    </script>
@endpush
