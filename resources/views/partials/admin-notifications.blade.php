@if (session('success') || session('error') || session('warning') || session('info'))
    <div id="admin-toast-container" class="fixed top-5 right-5 z-50 space-y-3" style="min-width:280px;max-width:400px;">
        @php
            $map = [
                'success' => ['title' => 'Exito', 'box' => 'bg-green-50 border-green-500', 'text' => 'text-green-800', 'sub' => 'text-green-700'],
                'error' => ['title' => 'Error', 'box' => 'bg-red-50 border-red-500', 'text' => 'text-red-800', 'sub' => 'text-red-700'],
                'warning' => ['title' => 'Atencion', 'box' => 'bg-yellow-50 border-yellow-500', 'text' => 'text-yellow-800', 'sub' => 'text-yellow-700'],
                'info' => ['title' => 'Informacion', 'box' => 'bg-blue-50 border-blue-500', 'text' => 'text-blue-800', 'sub' => 'text-blue-700'],
            ];
        @endphp

        @foreach (['success', 'error', 'warning', 'info'] as $type)
            @if (session($type))
                <div class="toast-notification border-l-4 rounded-lg shadow-lg p-4 {{ $map[$type]['box'] }}" data-auto-dismiss="5000">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold {{ $map[$type]['text'] }}">{{ $map[$type]['title'] }}</p>
                            <p class="text-sm {{ $map[$type]['sub'] }}">{{ session($type) }}</p>
                        </div>
                        <button class="close-toast text-gray-500 hover:text-gray-700" type="button" aria-label="Cerrar">x</button>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <script>
        (() => {
            const toasts = document.querySelectorAll('#admin-toast-container .toast-notification');
            toasts.forEach((toast) => {
                const timeout = setTimeout(() => dismissToast(toast), Number(toast.dataset.autoDismiss || 5000));
                const closeBtn = toast.querySelector('.close-toast');
                if (closeBtn) {
                    closeBtn.addEventListener('click', () => {
                        clearTimeout(timeout);
                        dismissToast(toast);
                    });
                }
            });

            function dismissToast(toast) {
                toast.classList.add('fade-out');
                setTimeout(() => {
                    toast.remove();
                    const container = document.getElementById('admin-toast-container');
                    if (container && container.children.length === 0) container.remove();
                }, 280);
            }
        })();
    </script>
@endif
