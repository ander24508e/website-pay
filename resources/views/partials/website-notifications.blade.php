<div id="website-toast-root" class="website-toast-root" aria-live="polite" aria-atomic="true"></div>

@if (session('success'))
    <div data-website-flash="success" data-website-message="{{ session('success') }}"></div>
@endif
@if (session('error'))
    <div data-website-flash="error" data-website-message="{{ session('error') }}"></div>
@endif
@if (session('warning'))
    <div data-website-flash="warning" data-website-message="{{ session('warning') }}"></div>
@endif
@if (session('info'))
    <div data-website-flash="info" data-website-message="{{ session('info') }}"></div>
@endif

<style>
    .website-toast-root {
        position: fixed;
        right: 1rem;
        bottom: 1rem;
        z-index: 1200;
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
        max-width: min(92vw, 380px);
    }

    .website-toast {
        border-radius: 10px;
        border: 1px solid rgba(255, 255, 255, 0.15);
        background: rgba(10, 16, 28, 0.96);
        color: #fff;
        box-shadow: 0 14px 36px rgba(0, 0, 0, 0.35);
        padding: 0.8rem 0.95rem;
        font-size: 0.86rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.55rem;
        transform: translateY(10px);
        opacity: 0;
        transition: opacity .18s ease, transform .18s ease;
    }

    .website-toast.show {
        opacity: 1;
        transform: translateY(0);
    }

    .website-toast.success { border-left: 4px solid #22c55e; }
    .website-toast.error { border-left: 4px solid #ef4444; }
    .website-toast.warning { border-left: 4px solid #f59e0b; }
    .website-toast.info { border-left: 4px solid #3b82f6; }

    .website-toast-icon {
        inline-size: 1rem;
        block-size: 1rem;
        flex: 0 0 1rem;
    }

    @media (max-width: 768px) {
        .website-toast-root {
            right: 0.75rem;
            left: 0.75rem;
            bottom: 0.75rem;
            max-width: none;
        }
    }
</style>

<script>
    (() => {
        const root = document.getElementById('website-toast-root');
        if (!root) return;

        const icons = {
            success: '✓',
            error: '✕',
            warning: '!',
            info: 'i',
        };

        function notify(type = 'info', message = '', duration = 3200) {
            if (!message) return;
            const toast = document.createElement('div');
            toast.className = `website-toast ${type}`;
            toast.innerHTML = `<span class="website-toast-icon">${icons[type] || icons.info}</span><span>${message}</span>`;
            root.appendChild(toast);

            requestAnimationFrame(() => toast.classList.add('show'));

            const dismiss = () => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 180);
            };

            setTimeout(dismiss, duration);
            toast.addEventListener('click', dismiss);
        }

        window.websiteNotify = notify;

        document.querySelectorAll('[data-website-flash]').forEach((el) => {
            notify(el.dataset.websiteFlash, el.dataset.websiteMessage || '');
        });
    })();
</script>
