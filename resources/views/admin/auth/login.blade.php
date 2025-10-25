@extends('layouts.guest')

@section('title', 'Login Admin')
@section('tagline', 'Masuk sebagai administrator')

@section('content')
    <form method="POST" action="{{ route('admin.login.submit') }}" class="form-grid">
        @csrf
        <div class="form-field">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-field">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" required>
            @error('password')
                <p class="field-error">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="primary-button">Masuk</button>
    </form>
@endsection
