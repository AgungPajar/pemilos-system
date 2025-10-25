@extends('layouts.guest')

@section('title', 'Masuk Pemilih')
@section('tagline', 'Gunakan token unik untuk memilih')

@section('content')
    <form method="POST" action="{{ route('voter.login.submit') }}" class="form-grid">
        @csrf
        <div class="form-field">
            <label for="code">Token Pemilih</label>
            <input type="text" id="code" name="code" value="{{ old('code') }}" placeholder="Misal: ABC123" required autofocus>
            <p class="field-hint">Masukkan kode token yang diberikan panitia. Token hanya dapat digunakan sekali.</p>
            @error('code')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="primary-button">Masuk</button>
        <p class="field-hint field-hint-link">Siswa PKL? <a href="{{ route('pkl.login') }}" class="field-link">Masuk dengan NIS &amp; tanggal lahir</a>.</p>
    </form>
@endsection
