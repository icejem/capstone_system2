@props(['status'])

@if ($status)
    <div
        {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600 transition-opacity duration-500']) }}
        data-auth-session-status
    >
        {{ $status }}
    </div>

    <script>
        (() => {
            const status = document.querySelector('[data-auth-session-status]');

            if (!status) {
                return;
            }

            window.setTimeout(() => {
                status.classList.add('opacity-0');

                window.setTimeout(() => {
                    status.remove();
                }, 500);
            }, 4000);
        })();
    </script>
@endif
