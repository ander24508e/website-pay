@if (session('success') || session('error') || session('warning') || session('info'))
    <div id="toast-container" class="fixed top-5 right-5 z-50 space-y-3" style="min-width: 280px; max-width: 400px;">
        
        {{-- Success Toast --}}
        @if (session('success'))
            <div class="toast-notification bg-green-50 border-l-4 border-green-500 rounded-lg shadow-lg p-4 transform transition-all duration-300 ease-in-out"
                 data-auto-dismiss="5000">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-green-800">Â¡Ã‰xito!</p>
                        <p class="text-sm text-green-700">{{ session('success') }}</p>
                    </div>
                    <button class="close-toast text-green-400 hover:text-green-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Error Toast --}}
        @if (session('error'))
            <div class="toast-notification bg-red-50 border-l-4 border-red-500 rounded-lg shadow-lg p-4 transform transition-all duration-300 ease-in-out"
                 data-auto-dismiss="6000">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-red-800">Â¡Error!</p>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                    <button class="close-toast text-red-400 hover:text-red-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Warning Toast --}}
        @if (session('warning'))
            <div class="toast-notification bg-yellow-50 border-l-4 border-yellow-500 rounded-lg shadow-lg p-4 transform transition-all duration-300 ease-in-out"
                 data-auto-dismiss="5000">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-yellow-800">Â¡AtenciÃ³n!</p>
                        <p class="text-sm text-yellow-700">{{ session('warning') }}</p>
                    </div>
                    <button class="close-toast text-yellow-400 hover:text-yellow-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        {{-- Info Toast --}}
        @if (session('info'))
            <div class="toast-notification bg-blue-50 border-l-4 border-blue-500 rounded-lg shadow-lg p-4 transform transition-all duration-300 ease-in-out"
                 data-auto-dismiss="4000">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0">
                        <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-blue-800">InformaciÃ³n</p>
                        <p class="text-sm text-blue-700">{{ session('info') }}</p>
                    </div>
                    <button class="close-toast text-blue-400 hover:text-blue-600">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

    </div>
<script>
        document.addEventListener('DOMContentLoaded', function() {
            const toasts = document.querySelectorAll('.toast-notification');
            
            toasts.forEach(toast => {
                const dismissTime = toast.getAttribute('data-auto-dismiss') || 5000;
                const timeout = setTimeout(() => {
                    dismissToast(toast);
                }, dismissTime);

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
                    const container = document.getElementById('toast-container');
                    if (container && container.children.length === 0) {
                        container.remove();
                    }
                }, 300);
            }
        });
    </script>
@endif