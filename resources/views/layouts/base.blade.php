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
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    @stack('styles')
</head>
<body class="app-body">
<div class="app-background">
    <div class="app-bg-grid"></div>
    <div class="app-aurora aurora-one"></div>
    <div class="app-aurora aurora-two"></div>
</div>
<div class="app-wrapper">
    <div class="app-chrome">
        @yield('body')
    </div>
</div>

@stack('scripts')
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    (function () {
        var activeForm = null;

        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!(form instanceof HTMLFormElement)) {
                return;
            }

            var confirmMessage = form.dataset.confirm;
            if (!confirmMessage) {
                return;
            }

            if (form.dataset.confirmed === 'true') {
                form.dataset.confirmed = '';
                return;
            }

            event.preventDefault();
            activeForm = form;

            var title = form.dataset.confirmTitle || 'Konfirmasi Tindakan';
            var confirmText = form.dataset.confirmButton || 'Ya, lanjutkan';
            var variant = form.dataset.confirmVariant || 'primary';

            Swal.fire({
                title: title,
                html: confirmMessage,
                icon: variant === 'danger' ? 'warning' : 'question',
                showCancelButton: true,
                confirmButtonText: confirmText,
                cancelButtonText: 'Batal',
                customClass: {
                    confirmButton: 'primary-button',
                    cancelButton: 'ghost-button'
                },
                buttonsStyling: false,
                reverseButtons: false
            }).then(function (result) {
                if (result.isConfirmed) {
                    activeForm.dataset.confirmed = 'true';
                    if (typeof activeForm.requestSubmit === 'function') {
                        activeForm.requestSubmit();
                    } else {
                        activeForm.submit();
                    }
                }
                activeForm = null;
            });
        }, true);
    })();
</script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
