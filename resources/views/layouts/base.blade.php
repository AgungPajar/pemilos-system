<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Pemilos System')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-Sb0y1xkL9Q6+B+M6UvJBGtewqpvkiFHl6mFvZjFeXa1lCXYwuyvUQ5niF8Lzf+j3nNVr7Di1xFJd1b9gC9fQxA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>
<body class="app-body">
<div class="app-background"></div>
<div class="app-wrapper">
    @yield('body')
</div>
@stack('scripts')
</body>
</html>
