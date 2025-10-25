@extends('layouts.guest')

@section('title', 'Login PKL')
@section('tagline', 'Masuk menggunakan NIS dan tanggal lahir')

@section('content')
    <form method="POST" action="{{ route('pkl.login.submit') }}" class="form-grid">
        @csrf
        <div class="form-field">
            <label for="nis">NIS</label>
            <input type="text" id="nis" name="nis" value="{{ old('nis') }}" placeholder="Masukkan NIS" required autofocus>
            @error('nis')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field">
            <label for="birth_date">Tanggal Lahir</label>
            <input type="date" id="birth_date" name="birth_date" value="{{ old('birth_date') }}" required>
            @error('birth_date')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="primary-button">Masuk</button>
        <p class="field-hint">
            Sudah memiliki token manual? <a href="{{ route('voter.login') }}">Masuk dengan token</a>.
        </p>
    </form>
@endsection
