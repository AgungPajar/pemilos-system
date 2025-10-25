@extends('layouts.voter')

@section('content')
    <section class="token-banner">
        <div>
            <p class="banner-label">Token aktif</p>
            <h2 class="banner-value">#{{ strtoupper(substr($token->code, 0, 3)) }}•••{{ strtoupper(substr($token->code, -3)) }}</h2>
        </div>
        <p class="banner-text">Pilih pasangan calon pilihanmu dengan bijak. Token ini akan otomatis tertutup setelah memilih.</p>
    </section>

    <section class="paslon-grid">
        @forelse ($paslons as $paslon)
            @php
                $missionPoints = array_filter(preg_split('/\r\n|\n|\r/', $paslon->mission ?? ''));
                $programPoints = array_filter(preg_split('/\r\n|\n|\r/', $paslon->program ?? ''));
            @endphp
            <article class="voter-card">
                <div class="card-media">
                    @if ($paslon->image_path)
                        <img src="{{ asset($paslon->image_path) }}" alt="{{ $paslon->display_name }}">
                    @else
                        <div class="paslon-thumb placeholder">{{ $paslon->initials }}</div>
                    @endif
                    <span class="badge badge-soft">Paslon {{ $paslon->order_number }}</span>
                </div>
                <div class="card-body">
                    <h3>{{ $paslon->leader_name }}</h3>
                    <p class="tagline small-tagline">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
                    @if ($paslon->tagline)
                        <p class="tagline">“{{ $paslon->tagline }}”</p>
                    @endif
                    <p class="summary">{!! nl2br(e(\Illuminate\Support\Str::limit($paslon->vision, 150))) !!}</p>
                    <div class="card-actions">
                        <button type="button" class="ghost-button" data-modal-trigger="modal-{{ $paslon->id }}">Detail</button>
                        <form method="POST" action="{{ route('voter.vote') }}" onsubmit="return confirm('Yakin memilih {{ $paslon->display_name }}?');">
                            @csrf
                            <input type="hidden" name="paslon_id" value="{{ $paslon->id }}">
                            <button type="submit" class="primary-button">Pilih Paslon</button>
                        </form>
                    </div>
                </div>
            </article>

            <div class="modal" id="modal-{{ $paslon->id }}">
                    <div class="modal-dialog">
                        <button type="button" class="modal-close" data-modal-close>&times;</button>
                        <header class="modal-header">
                            <span class="modal-badge">Paslon {{ $paslon->order_number }}</span>
                            <h3>{{ $paslon->leader_name }}</h3>
                            <p class="modal-meta">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
                            @if ($paslon->tagline)
                                <p class="modal-tagline">“{{ $paslon->tagline }}”</p>
                            @endif
                        </header>
                    <div class="modal-body">
                        <section>
                            <h4>Visi</h4>
                            <p>{!! nl2br(e($paslon->vision)) !!}</p>
                        </section>
                        <section>
                            <h4>Misi</h4>
                            <ul>
                                @forelse ($missionPoints as $point)
                                    <li>{{ trim($point) }}</li>
                                @empty
                                    <li>-</li>
                                @endforelse
                            </ul>
                        </section>
                        <section>
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
                        <form method="POST" action="{{ route('voter.vote') }}" onsubmit="return confirm('Konfirmasi memilih {{ $paslon->display_name }}?');">
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
