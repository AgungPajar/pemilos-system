@if (session('success'))
    <div class="alert alert-success">
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        <span>{{ session('error') }}</span>
    </div>
@endif

@if ($errors->has('general'))
    <div class="alert alert-error">
        <span>{{ $errors->first('general') }}</span>
    </div>
@endif
