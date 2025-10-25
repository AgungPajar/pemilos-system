@extends('layouts.admin')

@section('title', 'Tambah Paslon')
@section('subtitle', 'Masukkan detail pasangan calon baru')

@section('content')
    <form method="POST" action="{{ route('admin.paslon.store') }}" enctype="multipart/form-data" class="panel form-panel">
        @include('admin.paslon._form', ['paslon' => null, 'submitLabel' => 'Tambah'])
    </form>
@endsection
