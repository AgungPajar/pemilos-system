@extends('layouts.admin')

@section('title', 'Manajemen Token')
@section('subtitle', 'Atur token pemilih dan pantau penggunaannya')

@section('content')
    <div class="panel form-panel">
        <h3 class="panel-title">Generate Token Baru</h3>
        <form action="{{ route('admin.tokens.store') }}" method="POST" class="form-inline">
            @csrf
            <div class="form-field inline">
                <label for="amount">Jumlah Token</label>
                <input type="number" id="amount" name="amount" min="1" max="200" value="{{ old('amount', 10) }}" required>
                @error('amount')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-field inline">
                <label for="note">Catatan (opsional)</label>
                <input type="text" id="note" name="note" value="{{ old('note') }}" placeholder="Misal: Kelas XII RPL">
                @error('note')
                    <p class="field-error">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="primary-button">Generate</button>
        </form>

        @if (session('new_tokens'))
            <div class="panel mt">
                <h4>Token Baru</h4>
                <textarea readonly class="token-list">{{ session('new_tokens') }}</textarea>
                <p class="field-hint">Salin atau cetak token melalui tombol di bawah.</p>
            </div>
        @endif
    </div>

    <div class="panel-grid single">
        <section class="panel">
            <header class="panel-header">
                <div>
                    <h4>Daftar Token</h4>
                    <p>Status penggunaan token pemilih.</p>
                </div>
                <div class="panel-actions">
                    <a href="{{ route('admin.tokens.summary') }}" class="ghost-button">Ringkasan</a>
                    <a href="{{ route('admin.tokens.print', ['status' => $status]) }}" class="ghost-button" target="_blank">Print</a>
                </div>
            </header>
            <div class="filter-bar">
                <a href="{{ route('admin.tokens.index') }}"
                   class="chip {{ $status === null ? 'is-active' : '' }}">Semua</a>
                <a href="{{ route('admin.tokens.index', ['status' => 'unused']) }}"
                   class="chip {{ $status === 'unused' ? 'is-active' : '' }}">Belum Digunakan</a>
                <a href="{{ route('admin.tokens.index', ['status' => 'used']) }}"
                   class="chip {{ $status === 'used' ? 'is-active' : '' }}">Sudah Digunakan</a>
            </div>
            <div class="table-wrapper hide-sm">
                <table class="data-table">
                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode</th>
                        <th>Status</th>
                        <th>Paslon Pilihan</th>
                        <th>Catatan</th>
                        <th>Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($tokens as $token)
                        <tr>
                            <td>{{ $loop->iteration + ($tokens->currentPage() - 1) * $tokens->perPage() }}</td>
                            <td class="code-cell">{{ $token->code }}</td>
                            <td>
                                @if ($token->isUsed())
                                    <span class="badge success">Digunakan</span>
                                    <p class="badge-subtitle">{{ optional($token->used_at)->format('d M H:i') }}</p>
                                @else
                                    <span class="badge warning">Belum</span>
                                @endif
                            </td>
                            <td>
                                {{ optional($token->paslon)->display_name ?? optional($token->paslon)->name ?? '-' }}
                            </td>
                            <td>{{ $token->note ?? '-' }}</td>
                            <td>{{ $token->created_at->format('d M Y') }}</td>
                            <td class="table-actions">
                                @if (! $token->isUsed())
                        <form action="{{ route('admin.tokens.destroy', $token) }}" method="POST"
                            data-confirm="Hapus token ini?"
                            data-confirm-title="Hapus Token"
                            data-confirm-button="Ya, hapus"
                            data-confirm-variant="danger">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="ghost-button danger">Hapus</button>
                                    </form>
                                @else
                                    <span class="table-note">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">Belum ada token dibuat.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mobile-card-list show-sm">
                @forelse ($tokens as $token)
                    <article class="mobile-token-card">
                        <header>
                            <div class="token-code">{{ $token->code }}</div>
                            <div class="token-meta">
                                <span class="badge {{ $token->isUsed() ? 'success' : 'warning' }}">
                                    {{ $token->isUsed() ? 'Digunakan' : 'Belum' }}
                                </span>
                                @if ($token->isUsed())
                                    <span class="meta-time">{{ optional($token->used_at)->format('d M H:i') }}</span>
                                @endif
                            </div>
                        </header>
                        <section>
                            <p><strong>Paslon:</strong> {{ optional($token->paslon)->display_name ?? optional($token->paslon)->name ?? '-' }}</p>
                            <p><strong>Catatan:</strong> {{ $token->note ?? '-' }}</p>
                            <p><strong>Dibuat:</strong> {{ $token->created_at->format('d M Y') }}</p>
                        </section>
                        <footer>
                            @if (! $token->isUsed())
                      <form action="{{ route('admin.tokens.destroy', $token) }}" method="POST"
                          data-confirm="Hapus token ini?"
                          data-confirm-title="Hapus Token"
                          data-confirm-button="Ya, hapus"
                          data-confirm-variant="danger">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ghost-button danger">Hapus</button>
                                </form>
                            @else
                                <span class="table-note">Token sudah digunakan</span>
                            @endif
                        </footer>
                    </article>
                @empty
                    <p class="empty-state">Belum ada token dibuat.</p>
                @endforelse
            </div>
            <div class="pagination-bar">
                {{ $tokens->links('vendor.pagination.tokens') }}
            </div>
        </section>
    </div>
@endsection
