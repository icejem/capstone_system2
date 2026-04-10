@props(['status'])

@if ($status)
    <div
        {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 transition-opacity duration-500']) }}
        data-auth-session-status
        role="status"
    >
        <div class="flex items-start justify-between gap-3">
            <span>{{ $status }}</span>
            <button
                type="button"
                class="shrink-0 text-current opacity-80 transition-opacity hover:opacity-100"
                data-auth-session-close
                aria-label="Dismiss status message"
            >
                &times;
            </button>
        </div>
    </div>

    <script>
        (() => {
            const closeStatus = (status) => {
                if (!status || status.dataset.dismissed === 'true') {
                    return;
                }

                status.dataset.dismissed = 'true';
                status.classList.add('opacity-0');

                window.setTimeout(() => {
                    status.remove();
                }, 500);
            };

            document.querySelectorAll('[data-auth-session-status]').forEach((status) => {
                const closeButton = status.querySelector('[data-auth-session-close]');

                if (closeButton) {
                    closeButton.addEventListener('click', () => closeStatus(status));
                }

                window.setTimeout(() => {
                    closeStatus(status);
                }, 4000);
            });
        })();
    </script>
@endif
