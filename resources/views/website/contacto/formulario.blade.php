<section class="contact-form-card">
    <h3>Envíanos un Mensaje</h3>

    <form id="contact-booking-form" class="contact-form-grid" novalidate>
        <div>
            <label for="contact-nombre">Nombre Completo <span>*</span></label>
            <input type="text" id="contact-nombre" required minlength="3" placeholder="Tu nombre completo">
        </div>

        <div>
            <label for="contact-telefono">Teléfono de Contacto <span>*</span></label>
            <input type="tel" id="contact-telefono" required pattern="[0-9+\s()-]{10,15}"
                placeholder="+593 98 123 4546">
        </div>

        <div id="contact-vehicle-wrap" class="contact-select-field contact-vehicle-select-field">
            <label for="contact-vehicle">Servicio<span>*</span></label>
            <select id="contact-vehicle" class="contact-service-select contact-vehicle-select-main" required>
                <option value="" selected disabled hidden></option>
            </select>
        </div>

        <div>
            <label for="contact-vehicle-detail">Vehiculo <span>*</span></label>
            <input type="text" id="contact-vehicle-detail" required minlength="3"
                placeholder="Ej: Kia Picanto blanco, placa ABC-1234">
        </div>

        <div class="contact-two-cols">
            <div>
                <label for="contact-fecha">Fecha <span>*</span></label>
                <input type="date" id="contact-fecha" required>
                <p class="field-hint">Domingos (Mantenimiento)</p>
            </div>
            <div>
                <label for="contact-hora">Hora <span>*</span></label>
                <select id="contact-hora" class="contact-hour-select" required>
                    <option value="">Selecciona hora</option>
                    @for ($h = 8; $h <= 17; $h++)
                        @php($hour = str_pad((string) $h, 2, '0', STR_PAD_LEFT) . ':00')
                        <option value="{{ $hour }}">{{ $hour }}</option>
                    @endfor
                </select>
                <p class="field-hint">08:00 am - 18:00 pm</p>
            </div>
        </div>

        <div>
            <label for="contact-mensaje">Comentarios adicionales <small>(opcional)</small></label>
            <textarea id="contact-mensaje" rows="3" maxlength="500"
                placeholder="Ej: Necesito una mesa para 4, una camioneta, un combo o una reserva especial..."></textarea>
            <p class="field-hint">Escríbenos tu mensaje describiendo todo lo que necesites.</p>
        </div>

        <button type="button" id="contact-submit" class="contact-submit-btn">Reservar Ahora</button>
    </form>
</section>

@push('scripts')
    <script>
        (() => {
            const form = document.getElementById('contact-booking-form');
            const btn = document.getElementById('contact-submit');
            const fechaInput = document.getElementById('contact-fecha');

            if (!form || !btn || !fechaInput) return;

            const nombreInput = document.getElementById('contact-nombre');
            const telefonoInput = document.getElementById('contact-telefono');
            const vehicleInput = document.getElementById('contact-vehicle');
            const vehicleDetailInput = document.getElementById('contact-vehicle-detail');
            const horaInput = document.getElementById('contact-hora');
            const mensajeInput = document.getElementById('contact-mensaje');
            const waPhone = @json((string) ($empresa->telefono_contacto ?? ''));
            const reservableItems = @json(($contactReservableItems ?? collect())->values());

            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            fechaInput.setAttribute('min', `${yyyy}-${mm}-${dd}`);

            function showError(message) {
                window.websiteNotify?.('error', message);
            }

            function showSuccess(message) {
                window.websiteNotify?.('success', message);
            }

            function isSunday(dateText) {
                const date = new Date(`${dateText}T00:00:00`);
                return date.getDay() === 0;
            }

            function validate() {
                if (!nombreInput.value || nombreInput.value.trim().length < 3) return 'Ingresa tu nombre completo.';
                if (!telefonoInput.value.trim()) return 'Ingresa tu telefono de contacto.';
                if (!getSelectedVehicleContext().hasVehicleContext) {
                    return 'Selecciona servicio .';
                }
                if (!vehicleDetailInput.value || vehicleDetailInput.value.trim().length < 3) {
                    return 'Describe el vehiculo del cliente.';
                }
                if (!fechaInput.value) return 'Selecciona una fecha.';
                if (isSunday(fechaInput.value)) return 'Domingos no laboramos.';
                if (!horaInput.value) return 'Selecciona una hora.';
                return null;
            }

            function formatDate(dateText) {
                const [year, month, day] = dateText.split('-');
                return `${day}/${month}/${year}`;
            }

            function normalizeWaPhone(phone) {
                const digits = String(phone || '').replace(/\D+/g, '');
                if (!digits) return '';
                if (digits.startsWith('593')) return digits;
                if (digits.length === 10 && digits.startsWith('0')) return `593${digits.slice(1)}`;
                return digits;
            }

            function buildWhatsappMessage() {
                const vehicleContext = getSelectedVehicleContext();
                let msg = `NUEVA SOLICITUD\n\n`;
                msg += `Nombre: ${nombreInput.value.trim()}\n`;
                msg += `Telefono: ${telefonoInput.value.trim()}\n`;
                if (vehicleContext.itemLabel) msg += `Servicio: ${vehicleContext.itemLabel}\n`;
                if (vehicleContext.vehicleLabel) msg += `Tarifa seleccionada: ${vehicleContext.vehicleLabel}\n`;
                msg += `Vehiculo indicado: ${vehicleDetailInput.value.trim()}\n`;
                if (vehicleContext.priceLabel) msg += `Precio: ${vehicleContext.priceLabel}\n`;
                msg += `Fecha: ${formatDate(fechaInput.value)}\n`;
                msg += `Hora: ${horaInput.value}\n`;
                if (mensajeInput.value.trim()) msg += `\nComentarios:\n${mensajeInput.value.trim()}`;
                return encodeURIComponent(msg);
            }

            function getSelectedVehicleContext() {
                const option = vehicleInput?.selectedOptions?.[0];
                if (!option || !option.value) {
                    return {
                        hasVehicleContext: false,
                        itemId: null,
                        itemLabel: null,
                        priceId: null,
                        priceLabel: null,
                        vehicleId: null,
                        vehicleTypeId: null,
                        vehicleSpecificationId: null,
                        vehicleLabel: null,
                    };
                }

                return {
                    hasVehicleContext: true,
                    itemId: option.dataset.itemId ? Number(option.dataset.itemId) : null,
                    itemLabel: option.dataset.itemLabel || null,
                    priceId: option.dataset.priceId ? Number(option.dataset.priceId) : null,
                    priceLabel: option.dataset.priceLabel || null,
                    vehicleId: option.dataset.vehicleId ? Number(option.dataset.vehicleId) : null,
                    vehicleTypeId: option.dataset.vehicleTypeId ? Number(option.dataset.vehicleTypeId) : null,
                    vehicleSpecificationId: option.dataset.vehicleSpecificationId ? Number(option.dataset
                        .vehicleSpecificationId) : null,
                    vehicleLabel: option.textContent?.trim() || null,
                };
            }

            function money(value) {
                const amount = Number(value || 0);
                return `$${amount.toFixed(2)}`;
            }

            function renderVehicleOptions() {
                if (!vehicleInput) return;

                const selectedValue = vehicleInput.value;
                vehicleInput.required = true;
                vehicleInput.innerHTML = '<option value="" selected disabled hidden>Selecciona servicio</option>';

                reservableItems.forEach((item) => {
                    const prices = Array.isArray(item.precios_vehiculo) ? item.precios_vehiculo : [];
                    if (!prices.length) return;

                    const group = document.createElement('optgroup');
                    group.label = `${item.tipo_label || 'Servicio'} / ${item.nombre || 'Servicio'}`;

                    prices.forEach((price) => {
                        const option = document.createElement('option');
                        const priceLabel = money(price.price);
                        option.value = `service-price:${item.id}:${price.id}`;
                        option.dataset.itemId = String(item.id);
                        option.dataset.itemLabel = item.nombre || '';
                        option.dataset.priceId = String(price.id);
                        option.dataset.priceLabel = priceLabel;
                        option.dataset.vehicleSpecificationId = String(price.vehicle_specification_id ||
                            '');
                        option.dataset.vehicleTypeId = String(price.vehicle_type_id || '');
                        option.textContent =
                            `${price.vehicle_name || price.vehicle_type_name || 'Vehiculo'} - ${priceLabel}`;
                        group.appendChild(option);
                    });

                    vehicleInput.appendChild(group);
                });

                if (selectedValue && [...vehicleInput.options].some((option) => option.value === selectedValue)) {
                    vehicleInput.value = selectedValue;
                }
            }

            btn.addEventListener('click', () => {
                const error = validate();
                if (error) {
                    showError(error);
                    return;
                }

                const message = buildWhatsappMessage();
                const waTargetPhone = normalizeWaPhone(waPhone || telefonoInput.value);

                if (!waTargetPhone) {
                    showError('No existe un numero de WhatsApp configurado.');
                    return;
                }

                window.open(`https://wa.me/${waTargetPhone}?text=${message}`, '_blank');
                showSuccess('Solicitud preparada. Te contactaremos pronto.');
                form.reset();
                renderVehicleOptions();
            });

            fechaInput.addEventListener('change', () => {
                if (fechaInput.value && isSunday(fechaInput.value)) {
                    fechaInput.value = '';
                    showError('Domingos no laboramos.');
                }
            });
            renderVehicleOptions();
        })();
    </script>
@endpush
