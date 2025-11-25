@extends('layouts.voter')

@section('content')
    <section class="token-banner">
        <div>
            <p class="banner-label">Token aktif</p>
            <h2 class="banner-value">#{{ strtoupper(substr($token->code, 0, 3)) }}•••{{ strtoupper(substr($token->code, -3)) }}</h2>
        </div>
        <div class="token-banner-text">
            <p class="banner-text">Pastikan kamu sudah membaca visi dan misi setiap pasangan calon sebelum mengunci pilihan.</p>
            <p class="banner-subtext">Token akan otomatis tertutup setelah mengirim suara.</p>
        </div>
    </section>

    <section class="paslon-stage">
        <header class="paslon-stage-header">
            <div>
                <p class="stage-eyebrow">Pemilihan Ketua &amp; Wakil OSIS</p>
                <h2 class="stage-title">Kenali setiap paslon, pilih yang paling mewakili visi kamu</h2>
            </div>
            <p class="stage-meta">{{ $paslons->count() }} paslon siap dipilih. Tekan “Detail” untuk membaca visi &amp; misi lengkap sebelum mengirim pilihan.</p>
        </header>

        <div class="paslon-grid">
            @forelse ($paslons as $paslon)
                @php
                    $missionPoints = array_values(array_filter(preg_split('/\r\n|\n|\r/', $paslon->mission ?? '')));
                    $programPoints = array_values(array_filter(preg_split('/\r\n|\n|\r/', $paslon->program ?? '')));
                    $missionPreview = array_slice($missionPoints, 0, 3);
                    $programPreview = array_slice($programPoints, 0, 3);
                    $visionSnippet = \Illuminate\Support\Str::limit(strip_tags($paslon->vision ?? ''), 180);
                @endphp
                <article class="voter-card">
                    <div class="card-media">
                        @if ($paslon->image_path)
                            <img src="{{ asset($paslon->image_path) }}" alt="{{ $paslon->display_name }}">
                        @else
                            <div class="paslon-thumb placeholder">{{ $paslon->initials }}</div>
                        @endif
                        <span class="badge badge-soft badge-media">Paslon {{ $paslon->order_number }}</span>
                        {{-- tagline removed per request --}}
                    </div>
                    <div class="card-head">
                        <div class="card-head-primary">
                            <div class="paslon-headline">
                                <h3>{{ $paslon->leader_name }}</h3>
                                <p class="paslon-subtitle">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="paslon-meta">
                            <p class="paslon-meta-text">{{ count($missionPoints) }} misi &amp; {{ count($programPoints) }} program</p>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-info-grid">
                            <div class="card-section card-section-accent">
                                <p class="section-title">Visi</p>
                                <p class="summary">{!! nl2br(e($visionSnippet)) !!}</p>
                            </div>
                            <div class="card-section card-section-accent">
                                <p class="section-title">Sorotan Misi</p>
                                <ul class="mission-list">
                                    @forelse ($missionPreview as $index => $point)
                                        <li>
                                            <span class="mission-index">{{ $index + 1 }}</span>
                                            <span>{{ trim($point) }}</span>
                                        </li>
                                    @empty
                                        <li class="mission-empty">Belum ada misi yang dituliskan.</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                        @if ($programPreview)
                            <div class="card-section">
                                <p class="section-title">Program Unggulan</p>
                                <div class="program-chips">
                                    @foreach ($programPreview as $program)
                                        <span class="program-chip">{{ trim($program) }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        <div class="card-footer">
                            <button type="button" class="ghost-button" data-modal-trigger="modal-{{ $paslon->id }}">Detail paslon</button>
                            <form method="POST" action="{{ route('voter.vote') }}" class="vote-form"
                                data-confirm="Yakin memilih {{ $paslon->display_name }}?"
                                data-confirm-title="Kirim Suara"
                                data-confirm-button="Ya, pilih"
                                data-confirm-variant="danger">
                                @csrf
                                <input type="hidden" name="paslon_id" value="{{ $paslon->id }}">
                                <button type="submit" class="primary-button">Pilih Paslon</button>
                            </form>
                        </div>
                    </div>
                </article>

                <div class="modal" id="modal-{{ $paslon->id }}">
                    <div class="modal-dialog modal-dialog-wide">
                        <button type="button" class="modal-close" data-modal-close>&times;</button>
                        <header class="modal-header">
                            <span class="modal-badge">Paslon {{ $paslon->order_number }}</span>
                            <h3>{{ $paslon->leader_name }}</h3>
                            <p class="modal-meta">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
                            @if ($paslon->tagline)
                                <p class="modal-tagline">“{{ $paslon->tagline }}”</p>
                            @endif
                        </header>
                        <div class="modal-body modal-body-grid">
                            <section class="modal-section">
                                <h4>Visi</h4>
                                <p>{!! nl2br(e($paslon->vision)) !!}</p>
                            </section>
                            <section class="modal-section">
                                <h4>Misi</h4>
                                <ul>
                                    @forelse ($missionPoints as $point)
                                        <li>{{ trim($point) }}</li>
                                    @empty
                                        <li>-</li>
                                    @endforelse
                                </ul>
                            </section>
                            <section class="modal-section">
                                <h4>Program Kerja</h4>
                                <ul>
                                    @forelse ($programPoints as $point)
                                        <li>{{ trim($point) }}</li>
                                    @empty
                                        <li>-</li>
                                    @endforelse
                                </ul>
                            </section>
                        </div>
                        <footer class="modal-footer">
                            <form method="POST" action="{{ route('voter.vote') }}" class="vote-form"
                                data-confirm="Konfirmasi memilih {{ $paslon->display_name }}?"
                                data-confirm-title="Kirim Suara"
                                data-confirm-button="Kirim Pilihan"
                                data-confirm-variant="danger">
                                @csrf
                                <input type="hidden" name="paslon_id" value="{{ $paslon->id }}">
                                <button type="submit" class="primary-button">Konfirmasi Pilih</button>
                            </form>
                            <button type="button" class="ghost-button" data-modal-close>Tutup</button>
                        </footer>
                    </div>
                </div>
            @empty
                <p class="empty-state">Data paslon belum tersedia, hubungi panitia.</p>
            @endforelse
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-modal-trigger]').forEach(function (button) {
            button.addEventListener('click', function () {
                var id = this.getAttribute('data-modal-trigger');
                var modal = document.getElementById(id);
                if (modal) {
                    modal.classList.add('is-open');
                }
            });
        });

        document.querySelectorAll('[data-modal-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                var modal = this.closest('.modal');
                if (modal) {
                    modal.classList.remove('is-open');
                }
            });
        });

        document.querySelectorAll('.modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('is-open');
                }
            });
        });
    </script>
@endpush
