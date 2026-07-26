@once
    @push('scripts')
        <script>
            (function vehicleSpecificationsModals() {
                if (window.vehicleSpecificationsModalsReady) return;
                window.vehicleSpecificationsModalsReady = true;

                function openModal(modal) {
                    if (!modal) return;
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                    document.body.classList.add('overflow-hidden');
                }

                function closeModal(modal) {
                    if (!modal) return;
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }

                document.addEventListener('click', function (event) {
                    const openButton = event.target.closest('[data-open-vehicle-modal]');
                    if (openButton) {
                        openModal(document.getElementById(openButton.dataset.openVehicleModal));
                        return;
                    }

                    const closeButton = event.target.closest('[data-close-vehicle-modal]');
                    if (closeButton) {
                        closeModal(closeButton.closest('[data-vehicle-specifications-modal]'));
                        return;
                    }

                    const modal = event.target.matches('[data-vehicle-specifications-modal]')
                        ? event.target
                        : null;

                    if (modal) {
                        closeModal(modal);
                    }
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key !== 'Escape') return;

                    document
                        .querySelectorAll('[data-vehicle-specifications-modal]:not(.hidden)')
                        .forEach(closeModal);
                });
            })();
        </script>
    @endpush
@endonce
