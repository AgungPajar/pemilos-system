@extends('layouts.admin')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan statistik pemilihan OSIS')

@section('content')
    <div class="card-grid">
        <div class="stat-card">
            <p class="stat-label">Total Paslon</p>
            <h3 class="stat-value">{{ $totalPaslon }}</h3>
        </div>
        <div class="stat-card">
            <p class="stat-label">Total Pencoblos</p>
            <h3 class="stat-value">{{ $totalVoters }}</h3>
        </div>
        <div class="stat-card">
            <p class="stat-label">Token Belum Digunakan</p>
            <h3 class="stat-value">{{ $unusedTokens }}</h3>
        </div>
        <div class="stat-card">
            <p class="stat-label">Paslon Terfavorit</p>
            <h3 class="stat-value">
                {{ $leadingPaslon ? 'Paslon ' . $leadingPaslon->order_number : '-' }}
            </h3>
            <p class="stat-footnote">{{ $leadingPaslon ? $leadingPaslon->display_name : 'Belum ada suara' }}</p>
        </div>
    </div>

    <div class="panel-grid">
        <section class="panel">
            <header>
                <h4>Kartu Suara per Paslon</h4>
                <p>Detail jumlah pencoblos untuk setiap paslon.</p>
            </header>
            <div class="paslon-card-grid">
                @forelse ($paslonStats as $paslon)
                    <article class="paslon-card">
                        <div class="paslon-number">Paslon {{ $paslon->order_number }}</div>
                        <h5>{{ $paslon->display_name }}</h5>
                        <p class="paslon-votes">{{ $paslon->votes_count }} pencoblos</p>
                    </article>
                @empty
                    <p class="empty-state">Belum ada paslon yang didaftarkan.</p>
                @endforelse
            </div>
        </section>

        <section class="panel">
            <header>
                <h4>Grafik Pencoblos</h4>
                <p>Distribusi suara yang masuk.</p>
            </header>
            @php
                $maxVotes = max(1, $chartData->max('value'));
            @endphp
            <div class="chart-list">
                @forelse ($chartData as $item)
                    <div class="chart-row">
                        <div class="chart-label">
                            <span class="chart-title">{{ $item['label'] }}</span>
                            <span class="chart-subtitle">{{ $item['name'] }}</span>
                        </div>
                        <div class="chart-bar">
                            <div class="chart-bar-fill" style="width: {{ number_format(($item['value'] / $maxVotes) * 100, 2) }}%;"></div>
                        </div>
                        <div class="chart-value">{{ $item['value'] }}</div>
                    </div>
                @empty
                    <p class="empty-state">Suara belum masuk.</p>
                @endforelse
            </div>
        </section>
    </div>
@endsection
