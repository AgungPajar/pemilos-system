@extends('layouts.admin')

@section('title', 'Data Paslon')
@section('subtitle', 'Kelola pasangan calon ketua & wakil OSIS')

@section('content')
    <div class="toolbar">
        <a href="{{ route('admin.paslon.create') }}" class="primary-button">Tambah Paslon</a>
    </div>

    <div class="table-wrapper hide-sm">
        <table class="data-table">
            <thead>
            <tr>
                <th>No. Urut</th>
                <th>Gambar</th>
                <th>Ketua & Wakil</th>
                <th>Visi</th>
                <th>Misi</th>
                <th>Program</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse ($paslons as $paslon)
                <tr>
                    <td class="text-center">{{ $paslon->order_number }}</td>
                    <td>
                        @if ($paslon->image_path)
                            <img src="{{ asset($paslon->image_path) }}" alt="{{ $paslon->display_name }}" class="paslon-thumb">
                        @else
                            <div class="paslon-thumb placeholder">{{ $paslon->initials }}</div>
                        @endif
                    </td>
                    <td>
                        <div class="table-title">{{ $paslon->leader_name }}</div>
                        <p class="table-subtitle">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
                        @if ($paslon->tagline)
                            <p class="table-subtitle">{{ $paslon->tagline }}</p>
                        @endif
                    </td>
                    <td><div class="text-block">{!! nl2br(e($paslon->vision)) !!}</div></td>
                    <td><div class="text-block">{!! nl2br(e($paslon->mission)) !!}</div></td>
                    <td><div class="text-block">{!! nl2br(e($paslon->program)) !!}</div></td>
                    <td class="table-actions">
                        <a href="{{ route('admin.paslon.edit', $paslon) }}" class="ghost-button">Edit</a>
                        <form action="{{ route('admin.paslon.destroy', $paslon) }}" method="POST" onsubmit="return confirm('Hapus paslon ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ghost-button danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">Belum ada paslon yang ditambahkan.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mobile-card-list show-sm">
        @forelse ($paslons as $paslon)
            <article class="mobile-paslon-card">
                <div class="mobile-card-header">
                    <div class="mobile-card-media">
                        @if ($paslon->image_path)
                            <img src="{{ asset($paslon->image_path) }}" alt="{{ $paslon->display_name }}">
                        @else
                            <div class="paslon-thumb placeholder">{{ $paslon->initials }}</div>
                        @endif
                    </div>
                    <div class="mobile-card-title">
                        <span class="mobile-card-badge">Paslon {{ $paslon->order_number }}</span>
                        <h3>{{ $paslon->leader_name }}</h3>
                        <p class="mobile-card-subtitle">Wakil: {{ $paslon->deputy_name ?? '-' }}</p>
                        @if ($paslon->tagline)
                            <p class="mobile-card-subtitle">{{ $paslon->tagline }}</p>
                        @endif
                    </div>
                </div>
                <div class="mobile-card-body">
                    <div class="mobile-card-section">
                        <h4>Visi</h4>
                        <p>{!! nl2br(e(\Illuminate\Support\Str::limit($paslon->vision, 200))) !!}</p>
                    </div>
                    <div class="mobile-card-section">
                        <h4>Misi</h4>
                        <p>{!! nl2br(e(\Illuminate\Support\Str::limit($paslon->mission, 200))) !!}</p>
                    </div>
                    @if ($paslon->program)
                        <div class="mobile-card-section">
                            <h4>Program Kerja</h4>
                            <p>{!! nl2br(e(\Illuminate\Support\Str::limit($paslon->program, 200))) !!}</p>
                        </div>
                    @endif
                </div>
                <div class="mobile-card-actions">
                    <a href="{{ route('admin.paslon.edit', $paslon) }}" class="ghost-button">Edit</a>
                    <form action="{{ route('admin.paslon.destroy', $paslon) }}" method="POST" onsubmit="return confirm('Hapus paslon ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ghost-button danger">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <p class="empty-state">Belum ada paslon yang ditambahkan.</p>
        @endforelse
    </div>
@endsection
