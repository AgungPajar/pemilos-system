@extends('layouts.admin')

@section('title', 'Edit Paslon')
@section('subtitle', 'Perbarui informasi pasangan calon')

@section('content')
    <form method="POST" action="{{ route('admin.paslon.update', $paslon) }}" enctype="multipart/form-data" class="panel form-panel">
        @method('PUT')
        @include('admin.paslon._form', ['paslon' => $paslon, 'submitLabel' => 'Simpan Perubahan'])
    </form>
@endsection
