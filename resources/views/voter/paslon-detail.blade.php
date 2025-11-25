@extends('layouts.voter')

@section('content')
    <section class="paslon-stage">
        <header class="paslon-stage-header">
            <div>
                <a href="{{ route('voter.dashboard') }}" class="ghost-button" style="display: inline-flex; align-items: center; gap: 8px; margin-bottom: 16px;">
                    <span>←</span> Kembali
                </a>
                <p class="stage-eyebrow">Detail Paslon {{ $paslon->order_number }}</p>
                <h2 class="stage-title">{{ $paslon->leader_name }}</h2>
                <p class="stage-meta">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
            </div>
        </header>

        <div class="paslon-detail-container">
            @php
                $missionPoints = array_values(array_filter(preg_split('/\r\n|\n|\r/', $paslon->mission ?? '')));
                $programPoints = array_values(array_filter(preg_split('/\r\n|\n|\r/', $paslon->program ?? '')));
            @endphp

            <div class="detail-grid">
                <div class="detail-media">
                    @if ($paslon->image_path)
                        <img src="{{ asset($paslon->image_path) }}" alt="{{ $paslon->display_name }}">
                    @else
                        <div class="paslon-thumb placeholder">{{ $paslon->initials }}</div>
                    @endif
                </div>

                <div class="detail-content">
                    {{-- Tagline hidden in detail page --}}

                    <div class="detail-section">
                        <h3>Visi</h3>
                        @if ($paslon->vision)
                            <p>{!! nl2br(e($paslon->vision)) !!}</p>
                        @else
                            <p class="text-muted">Belum ada visi yang dituliskan.</p>
                        @endif
                    </div>

                    <div class="detail-section">
                        <h3>Misi</h3>
                        @if (count($missionPoints) > 0)
                            <ul class="detail-list">
                                @foreach ($missionPoints as $index => $point)
                            <li>
                                <span class="mission-index">{{ $index + 1 }}</span>
                                <span>{{ trim($point) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Belum ada misi yang dituliskan.</p>
                @endif
                    </div>

                    <div class="detail-section">
                        <h3>Program Kerja</h3>
                        @if (count($programPoints) > 0)
                            <ul class="detail-list">
                                @foreach ($programPoints as $index => $point)
                                    <li>
                                        <span class="mission-index">{{ $index + 1 }}</span>
                                        <span>{{ trim($point) }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted">Belum ada program yang dituliskan.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="detail-actions">
                <form method="POST" action="{{ route('voter.vote') }}" class="vote-form"
                    data-confirm="Yakin memilih {{ $paslon->display_name }}?"
                    data-confirm-title="Kirim Suara"
                    data-confirm-button="Ya, pilih"
                    data-confirm-variant="danger">
                    @csrf
                    <input type="hidden" name="paslon_id" value="{{ $paslon->id }}">
                    <button type="submit" class="primary-button" style="width: 100%;">Pilih Paslon Ini</button>
                </form>
                <a href="{{ route('voter.dashboard') }}" class="ghost-button" style="width: 100%; text-align: center;">Kembali ke Dashboard</a>
            </div>
        </div>
    </section>
@endsection
