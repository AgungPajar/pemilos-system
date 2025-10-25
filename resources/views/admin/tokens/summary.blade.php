@extends('layouts.admin')

@section('title', 'Ringkasan Suara')
@section('subtitle', 'Analisis distribusi suara dan penggunaan token')

@section('content')
    <div class="panel-grid">
        <section class="panel">
            <header class="panel-header">
                <div>
                    <h4>Statistik Token</h4>
                    <p>Kondisi penggunaan token terbaru.</p>
                </div>
            </header>
            <div class="stat-card-grid">
                <div class="stat-card">
                    <p class="stat-label">Total Token</p>
                    <h3 class="stat-value">{{ $totalTokens }}</h3>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Token Terpakai</p>
                    <h3 class="stat-value">{{ $totalVoted }}</h3>
                </div>
                <div class="stat-card">
                    <p class="stat-label">Token Tersisa</p>
                    <h3 class="stat-value">{{ $unusedTokens }}</h3>
                </div>
            </div>
        </section>

        <section class="panel">
            <header class="panel-header">
                <div>
                    <h4>Perolehan Suara per Paslon</h4>
                    <p>Jumlah pemilih yang masuk untuk setiap paslon.</p>
                </div>
            </header>
            <div class="paslon-card-grid">
                @forelse ($paslonStats as $paslon)
                    <article class="paslon-card">
                        <div class="paslon-number">Paslon {{ $paslon->order_number }}</div>
                        <h5>{{ $paslon->display_name }}</h5>
                        <p class="paslon-votes">{{ $paslon->votes_count }} suara</p>
                    </article>
                @empty
                    <p class="empty-state">Belum ada paslon yang didaftarkan.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
