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

<div class="modal confirm-modal" id="app-confirm-modal" aria-hidden="true" role="dialog" aria-modal="true">
    <div class="modal-dialog">
        <button type="button" class="modal-close" data-confirm-close aria-label="Tutup konfirmasi">&times;</button>
        <header class="modal-header">
            <span class="modal-badge">Konfirmasi</span>
            <h3 data-confirm-title>Konfirmasi Tindakan</h3>
            <p class="modal-meta" data-confirm-message>Pilih tombol konfirmasi untuk melanjutkan.</p>
        </header>
        <footer class="modal-footer">
            <button type="button" class="ghost-button" data-confirm-cancel>Batal</button>
            <button type="button" class="primary-button" data-confirm-accept>Ya, lanjutkan</button>
        </footer>
    </div>
</div>

@stack('scripts')
<script>
    (function () {
        var modal = document.getElementById('app-confirm-modal');
        if (!modal) {
            return;
        }

        var titleEl = modal.querySelector('[data-confirm-title]');
        var messageEl = modal.querySelector('[data-confirm-message]');
        var confirmBtn = modal.querySelector('[data-confirm-accept]');
        var cancelBtn = modal.querySelector('[data-confirm-cancel]');
        var closeBtns = modal.querySelectorAll('[data-confirm-close]');
        var activeForm = null;

        var setVariant = function (variant) {
            confirmBtn.classList.remove('danger');
            if (variant === 'danger') {
                confirmBtn.classList.add('danger');
            }
        };

        var openModal = function (options) {
            titleEl.textContent = options.title || 'Konfirmasi Tindakan';
            messageEl.textContent = options.message || 'Apakah Anda yakin ingin melanjutkan?';
            confirmBtn.textContent = options.confirmText || 'Ya, lanjutkan';
            setVariant(options.variant || 'primary');
            modal.classList.add('is-open');
            modal.removeAttribute('aria-hidden');
            window.setTimeout(function () {
                confirmBtn.focus();
            }, 60);
        };

        var closeModal = function () {
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            setVariant();
            activeForm = null;
        };

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

            openModal({
                title: form.dataset.confirmTitle,
                message: confirmMessage,
                confirmText: form.dataset.confirmButton,
                variant: form.dataset.confirmVariant
            });
        }, true);

        var submitActiveForm = function () {
            if (!activeForm) {
                return;
            }

            activeForm.dataset.confirmed = 'true';
            if (typeof activeForm.requestSubmit === 'function') {
                activeForm.requestSubmit();
            } else {
                activeForm.submit();
            }
            closeModal();
        };

        confirmBtn.addEventListener('click', submitActiveForm);

        var cancelHandler = function () {
            closeModal();
        };

        cancelBtn.addEventListener('click', cancelHandler);
        closeBtns.forEach(function (btn) {
            btn.addEventListener('click', cancelHandler);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.classList.contains('is-open')) {
                closeModal();
            }
        });
    })();
</script>
</body>
</html>
