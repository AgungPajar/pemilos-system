@extends('layouts.admin')

@section('title', 'Siswa PKL')
@section('subtitle', 'Kelola daftar siswa praktik kerja lapangan dan token pemilihan')

@section('content')
    <div class="card-grid">
        <div class="stat-card">
            <p class="stat-label">Total Siswa PKL</p>
            <h3 class="stat-value">{{ $total }}</h3>
        </div>
        <div class="stat-card">
            <p class="stat-label">Sudah Diberi Token</p>
            <h3 class="stat-value">{{ $withToken }}</h3>
        </div>
        <div class="stat-card">
            <p class="stat-label">Token Sudah Digunakan</p>
            <h3 class="stat-value">{{ $usedTokens }}</h3>
        </div>
    </div>

    <div class="panel-grid single">
        <section class="panel form-panel">
            <header class="panel-header">
                <div>
                    <h4>Tambah / Perbarui Siswa PKL</h4>
                    <p>Isi data siswa PKL. Token akan dibuat otomatis oleh sistem.</p>
                </div>
            </header>
            <form method="POST" action="{{ route('admin.pkl-students.store') }}" class="form-grid">
                @csrf
                <div class="form-field">
                    <label for="name">Nama Siswa</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field">
                    <label for="nis">NIS</label>
                    <input type="text" id="nis" name="nis" value="{{ old('nis') }}" required>
                    @error('nis') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field split">
                    <div>
                        <label for="jk">Jenis Kelamin</label>
                        <select id="jk" name="jk">
                            <option value="">-- Pilih --</option>
                            <option value="L" @selected(old('jk') === 'L')>Laki-laki</option>
                            <option value="P" @selected(old('jk') === 'P')>Perempuan</option>
                        </select>
                        @error('jk') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="kelas">Kelas</label>
                        <input type="text" id="kelas" name="kelas" value="{{ old('kelas') }}" required>
                        @error('kelas') <p class="field-error">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="form-field split">
                    {{-- nisn and tmp_lahir removed --}}
                </div>
                <div class="form-field">
                    <label for="tgl_lahir">Tanggal Lahir</label>
                    <input type="date" id="tgl_lahir" name="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
                    @error('tgl_lahir') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <div class="form-field full-width">
                    <p class="field-hint">Token akan dibuat dan tertaut otomatis ketika data disimpan.</p>
                </div>
                <div class="form-actions">
                    <button type="submit" class="primary-button">Simpan</button>
                    <button type="reset" class="ghost-button">Reset</button>
                </div>
            </form>
        </section>

        <section class="panel">
            <header class="panel-header">
                <div>
                    <h4>Import Siswa PKL</h4>
                    <p>Gunakan template berikut untuk mempercepat pengisian. Token akan dibuat otomatis.</p>
                </div>
                <div class="panel-actions">
                    <a href="{{ route('admin.pkl-students.template') }}" class="ghost-button">Unduh Template</a>
                </div>
            </header>
            <form method="POST" action="{{ route('admin.pkl-students.import') }}" enctype="multipart/form-data" class="form-inline">
                @csrf
                <div class="form-field inline">
                    <label for="import_file">File Import</label>
                    <input type="file" id="import_file" name="file" accept=".csv,.xls,.xlsx,.xml" required>
                    @error('file') <p class="field-error">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="primary-button">Import</button>
                <p class="field-hint">Unggah template Excel (.xls/.xlsx) yang diunduh dari sistem atau file .csv.</p>
            </form>
            @if (session('import_errors') && count(session('import_errors')))
                <div class="panel mt">
                    <h4>Catatan Import</h4>
                    <ul class="import-errors">
                        @foreach (session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </section>
    </div>

    <section class="panel">
        <header class="panel-header">
            <div>
                <h4>Daftar Siswa PKL</h4>
                <p>Data siswa praktik kerja lapangan beserta token pemilihan.</p>
            </div>
            <form method="GET" action="{{ route('admin.pkl-students.index') }}" class="form-inline">
                <div class="form-field inline">
                    <label for="search" class="sr-only">Cari</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" placeholder="Cari nama, NIS, atau kelas">
                </div>
                <button type="submit" class="ghost-button">Cari</button>
            </form>
        </header>

        <div class="table-wrapper hide-sm">
            <table class="data-table">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Token</th>
                    <th>Status Token</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($students as $student)
                    <tr>
                        <td>
                            <div class="table-title">{{ $student->name }}</div>
                            <p class="table-subtitle">{{ $student->jk === 'L' ? 'Laki-laki' : ($student->jk === 'P' ? 'Perempuan' : '-') }}</p>
                        </td>
                        <td>{{ $student->nis }}</td>
                        <td>{{ $student->kelas }}</td>
                        <td>{{ optional($student->token)->code ?? '-' }}</td>
                        <td>
                            @if ($student->token && $student->token->isUsed())
                                <span class="badge success">Sudah Memilih</span>
                            @elseif ($student->token)
                                <span class="badge warning">Belum Memilih</span>
                            @else
                                <span class="badge">Belum ada token</span>
                            @endif
                        </td>
                        <td class="table-actions">
                    <form action="{{ route('admin.pkl-students.destroy', $student) }}" method="POST"
                        data-confirm="Hapus data siswa PKL ini?"
                        data-confirm-title="Hapus Data"
                        data-confirm-button="Ya, hapus"
                        data-confirm-variant="danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ghost-button danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">Belum ada data siswa PKL.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mobile-card-list show-sm">
            @forelse ($students as $student)
                <article class="mobile-paslon-card">
                    <div class="mobile-card-header">
                        <div class="mobile-card-title">
                            <span class="mobile-card-badge">{{ $student->kelas }}</span>
                            <h3>{{ $student->name }}</h3>
                            <p class="mobile-card-subtitle">NIS: {{ $student->nis }}</p>
                        </div>
                    </div>
                    <div class="mobile-card-body">
                        <div class="mobile-card-section">
                            <h4>Token</h4>
                            <p>{{ optional($student->token)->code ?? '-' }}</p>
                        </div>
                        <div class="mobile-card-section">
                            <h4>Status</h4>
                            @if ($student->token && $student->token->isUsed())
                                <p>Token sudah digunakan.</p>
                            @elseif ($student->token)
                                <p>Token belum digunakan.</p>
                            @else
                                <p>Belum ada token.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mobile-card-actions">
                <form action="{{ route('admin.pkl-students.destroy', $student) }}" method="POST"
                    data-confirm="Hapus data siswa PKL ini?"
                    data-confirm-title="Hapus Data"
                    data-confirm-button="Ya, hapus"
                    data-confirm-variant="danger">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="ghost-button danger">Hapus</button>
                        </form>
                    </div>
                </article>
            @empty
                <p class="empty-state">Belum ada data siswa PKL.</p>
            @endforelse
        </div>

        <div class="pagination-bar">
            {{ $students->links('vendor.pagination.tokens') }}
        </div>
    </section>
@endsection
