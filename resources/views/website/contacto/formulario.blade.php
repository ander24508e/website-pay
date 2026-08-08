<section class="contact-form-card">
    <h3>Envíanos un Mensaje</h3>

    <form id="contact-booking-form" class="contact-form-grid" novalidate>
        <div>
            <label for="contact-nombre">Nombre Completo <span>*</span></label>
            <input type="text" id="contact-nombre" required minlength="3" placeholder="Tu nombre completo">
        </div>

        <div>
            <label for="contact-telefono">Teléfono de Contacto <span>*</span></label>
            <input type="tel" id="contact-telefono" required pattern="[0-9+\s()-]{10,15}" placeholder="+593 98 123 4546">
        </div>

        <div>
            <label for="contact-servicio">Servicio que deseas agendar <span>*</span></label>
            <select id="contact-servicio" class="contact-service-select select2" required
                data-placeholder="Busca servicio" data-select2-manual="true">
                <option value=""></option>
                @php
                    $uniqueItems = collect($reservableItems)
                        ->unique('id')
                        ->values()
                        ->groupBy(function ($item) {
                            return data_get($item, 'categoria', 'Sin categoría');
                        })
                        ->sortKeys();
                @endphp
                @foreach ($uniqueItems as $categoria => $items)
                    <optgroup label="{{ $categoria ?: 'Sin categoría' }}">
                        @foreach ($items as $item)
                            @php
                                $tipo = data_get($item, 'tipo_label', 'Catálogo');
                                $nombre = data_get($item, 'nombre', 'Sin nombre');
                                $displayText = trim("{$tipo} / {$nombre}");
                            @endphp
                            <option value="{{ data_get($item, 'id') }}">
                                {{ $displayText }}
                            </option>
                        @endforeach
                    </optgroup>
                @endforeach
            </select>
        </div>

        <div id="contact-vehicle-wrap" hidden>
            <label for="contact-vehicle">Vehículo o tipo de vehículo <span>*</span></label>
            <select id="contact-vehicle" class="contact-service-select select2"
                data-placeholder="Busca vehículo o tipo" data-select2-manual="true">
                <option value=""></option>
            </select>
            <p class="field-hint" id="contact-vehicle-hint">Selecciona el vehículo para calcular la reserva.</p>
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
            <textarea id="contact-mensaje" rows="3" maxlength="500" placeholder="Ej: Necesito una mesa para 4, una camioneta, un combo o una reserva especial..."></textarea>
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
    const servicioInput = document.getElementById('contact-servicio');
    const vehicleWrap = document.getElementById('contact-vehicle-wrap');
    const vehicleInput = document.getElementById('contact-vehicle');
    const vehicleHint = document.getElementById('contact-vehicle-hint');
    const horaInput = document.getElementById('contact-hora');
    const mensajeInput = document.getElementById('contact-mensaje');
    const waPhone = @json((string) ($empresa->telefono_contacto ?? ''));
    const catalogItems = @json($reservableItems->values());
    const customerVehicles = @json(($customerVehicles ?? collect())->values());
    const vehicleSpecifications = @json(($vehicleSpecifications ?? collect())->values());
    const catalogItemsById = new Map(catalogItems.map((item) => [Number(item.id), item]));
    const contactCard = form.closest('.contact-form-card');

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
        if (!nombreInput.value || nombreInput.value.trim().length < 3) return 'Ingresa tu nombre completo (mínimo 3 caracteres).';
        if (!telefonoInput.value.trim()) return 'Ingresa tu teléfono de contacto.';
        if (!servicioInput.value) return 'Selecciona un ítem del catálogo.';
        const selectedItem = getSelectedItem();
        if (selectedItem?.requiere_tipo_vehiculo && !getSelectedVehicleContext().hasVehicleContext) {
            return 'Selecciona el vehículo o tipo de vehículo para esta reserva.';
        }
        if (!fechaInput.value) return 'Selecciona una fecha.';
        if (isSunday(fechaInput.value)) return 'No se agendan reservas en domingo.';
        if (!horaInput.value) return 'Selecciona una hora.';
        return null;
    }

    function formatDate(dateText) {
        const [y, m, d] = dateText.split('-');
        return `${d}/${m}/${y}`;
    }

    function normalizeWaPhone(phone) {
        const digits = String(phone || '').replace(/\D+/g, '');
        if (!digits) return '';
        if (digits.startsWith('593')) return digits;
        if (digits.length === 10 && digits.startsWith('0')) return `593${digits.slice(1)}`;
        return digits;
    }

    function buildWhatsappMessage() {
        const selectedOption = servicioInput.options[servicioInput.selectedIndex];
        const itemName = selectedOption?.text || 'Item';
        const vehicleContext = getSelectedVehicleContext();
        let msg = `NUEVA CITA\n\n`;
        msg += `Nombre: ${nombreInput.value.trim()}\n`;
        msg += `Teléfono: ${telefonoInput.value.trim()}\n`;
        msg += `Item: ${itemName}\n`;
        if (vehicleContext.vehicleLabel) msg += `Vehículo: ${vehicleContext.vehicleLabel}\n`;
        msg += `Fecha: ${formatDate(fechaInput.value)}\n`;
        msg += `Hora: ${horaInput.value}\n`;
        if (mensajeInput.value.trim()) msg += `\nComentarios:\n${mensajeInput.value.trim()}`;
        return encodeURIComponent(msg);
    }

    function formatCatalogPrice(value) {
        return `$${Number(value || 0).toFixed(2)}`;
    }

    function canUseSelect2(select) {
        return Boolean(select && window.jQuery?.fn?.select2);
    }

    function destroySelect2(select) {
        if (!canUseSelect2(select)) return;

        const $select = window.jQuery(select);
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }
    }

    function initContactSelect2(select, placeholder) {
        if (!canUseSelect2(select)) return;

        const $select = window.jQuery(select);
        if ($select.hasClass('select2-hidden-accessible')) {
            $select.select2('destroy');
        }

        $select.select2({
            width: '100%',
            placeholder: placeholder || select.dataset.placeholder || 'Buscar',
            allowClear: true,
            dropdownParent: window.jQuery(contactCard || document.body),
            dropdownCssClass: 'website-vehicle-select-dropdown contact-select-dropdown',
            selectionCssClass: 'website-vehicle-select-selection contact-select-selection',
            escapeMarkup: (markup) => markup,
            templateResult: (data) => {
                if (!data.id) {
                    return null;
                }

                return data.text;
            },
            language: {
                noResults: () => 'No encontramos resultados',
                searching: () => 'Buscando...',
            },
        });
    }

    function getSelectedItem() {
        return catalogItemsById.get(Number(servicioInput.value || 0)) || null;
    }

    function getVehiclePrices(item) {
        return Array.isArray(item?.precios_vehiculo) ? item.precios_vehiculo : [];
    }

    function getVehiclePriceEntry(item, vehicleTypeId, vehicleSpecificationId = null) {
        const prices = getVehiclePrices(item);

        if (vehicleSpecificationId) {
            const exactPrice = prices.find((price) => Number(price.vehicle_specification_id) === Number(vehicleSpecificationId));
            if (exactPrice) return exactPrice;
        }

        return prices.find((price) => !price.vehicle_specification_id && Number(price.vehicle_type_id) === Number(vehicleTypeId)) || null;
    }

    function setVehicleOptionPriceData(option, priceEntry, fallbackPrice) {
        const price = priceEntry?.price !== null && priceEntry?.price !== undefined
            ? Number(priceEntry.price)
            : Number(fallbackPrice || 0);

        option.dataset.price = String(price);
        option.dataset.durationMinutes = priceEntry?.duration_minutes ? String(priceEntry.duration_minutes) : '';
        option.dataset.priceDescription = priceEntry?.description || '';

        return price;
    }

    function getSelectedVehicleContext() {
        const option = vehicleInput?.selectedOptions?.[0];
        if (!option || !option.value) {
            return {
                hasVehicleContext: false,
                vehicleId: null,
                vehicleTypeId: null,
                vehicleSpecificationId: null,
                vehicleLabel: null,
            };
        }

        return {
            hasVehicleContext: true,
            vehicleId: option.dataset.vehicleId ? Number(option.dataset.vehicleId) : null,
            vehicleTypeId: option.dataset.vehicleTypeId ? Number(option.dataset.vehicleTypeId) : null,
            vehicleSpecificationId: option.dataset.vehicleSpecificationId ? Number(option.dataset.vehicleSpecificationId) : null,
            vehicleLabel: option.textContent?.trim() || null,
        };
    }

    function updateVehicleHint() {
        if (!vehicleHint || !vehicleInput) return;
        const option = vehicleInput.selectedOptions[0];

        if (!option?.value) {
            vehicleHint.textContent = 'Selecciona el vehículo para calcular la reserva.';
            return;
        }

        const price = option.dataset.price ? formatCatalogPrice(option.dataset.price) : null;
        const duration = option.dataset.durationMinutes ? ` Duración aproximada: ${option.dataset.durationMinutes} min.` : '';
        vehicleHint.textContent = price ? `Precio calculado: ${price}.${duration}` : 'Vehículo seleccionado.';
    }

    function renderVehicleOptions(item) {
        if (!vehicleWrap || !vehicleInput) return;

        destroySelect2(vehicleInput);

        const prices = getVehiclePrices(item);
        const requiresVehicle = Boolean(item?.requiere_tipo_vehiculo) && prices.length > 0;
        const basePrice = Number(item?.precio_base ?? item?.precio ?? 0);

        vehicleWrap.hidden = !requiresVehicle;
        vehicleInput.required = requiresVehicle;
        vehicleInput.innerHTML = '<option value=""></option>';

        if (!requiresVehicle) {
            updateVehicleHint();
            return;
        }

        const pricedSpecificationIds = new Set(prices
            .filter((price) => price.vehicle_specification_id)
            .map((price) => Number(price.vehicle_specification_id)));
        const fallbackTypeIds = new Set(prices
            .filter((price) => !price.vehicle_specification_id)
            .map((price) => Number(price.vehicle_type_id)));
        const renderedPriceIds = new Set();

        const compatibleVehicles = customerVehicles.filter((vehicle) =>
            pricedSpecificationIds.has(Number(vehicle.vehicle_specification_id)) ||
            fallbackTypeIds.has(Number(vehicle.vehicle_type_id))
        );

        if (compatibleVehicles.length) {
            const group = document.createElement('optgroup');
            group.label = 'Mis vehículos';
            compatibleVehicles.forEach((vehicle) => {
                const priceEntry = getVehiclePriceEntry(item, vehicle.vehicle_type_id, vehicle.vehicle_specification_id);
                const option = document.createElement('option');
                option.value = `vehicle:${vehicle.id}`;
                option.dataset.vehicleId = String(vehicle.id);
                option.dataset.vehicleSpecificationId = String(vehicle.vehicle_specification_id);
                option.dataset.vehicleTypeId = String(vehicle.vehicle_type_id || '');
                const price = setVehicleOptionPriceData(option, priceEntry, basePrice);
                option.textContent = `${vehicle.label} - ${formatCatalogPrice(price)}`;
                group.appendChild(option);
                if (priceEntry?.id) renderedPriceIds.add(Number(priceEntry.id));
            });
            vehicleInput.appendChild(group);
        }

        const compatibleSpecifications = vehicleSpecifications.filter((specification) =>
            pricedSpecificationIds.has(Number(specification.id)) ||
            fallbackTypeIds.has(Number(specification.vehicle_type_id))
        );

        if (compatibleSpecifications.length) {
            const group = document.createElement('optgroup');
            group.label = compatibleVehicles.length ? 'Otros vehículos' : 'Vehículos disponibles';
            compatibleSpecifications.forEach((specification) => {
                const priceEntry = getVehiclePriceEntry(item, specification.vehicle_type_id, specification.id);
                const option = document.createElement('option');
                option.value = `spec:${specification.id}`;
                option.dataset.vehicleSpecificationId = String(specification.id);
                option.dataset.vehicleTypeId = String(specification.vehicle_type_id || '');
                const price = setVehicleOptionPriceData(option, priceEntry, basePrice);
                option.textContent = `${specification.name} - ${formatCatalogPrice(price)}`;
                group.appendChild(option);
                if (priceEntry?.id) renderedPriceIds.add(Number(priceEntry.id));
            });
            vehicleInput.appendChild(group);
        }

        const directPrices = prices.filter((price) => !renderedPriceIds.has(Number(price.id)));
        if (directPrices.length) {
            const group = document.createElement('optgroup');
            group.label = compatibleVehicles.length || compatibleSpecifications.length
                ? 'Otros precios disponibles'
                : 'Vehículos disponibles';

            directPrices.forEach((priceEntry) => {
                const vehicleTypeId = Number(priceEntry.vehicle_type_id);
                const vehicleSpecificationId = Number(priceEntry.vehicle_specification_id || 0);
                const option = document.createElement('option');
                const price = setVehicleOptionPriceData(option, priceEntry, basePrice);
                option.value = vehicleSpecificationId ? `spec:${vehicleSpecificationId}` : `type:${vehicleTypeId}`;
                if (vehicleSpecificationId) option.dataset.vehicleSpecificationId = String(vehicleSpecificationId);
                option.dataset.vehicleTypeId = String(vehicleTypeId);
                option.textContent = `${priceEntry.vehicle_name || priceEntry.vehicle_type_name || 'Vehículo'} - ${formatCatalogPrice(price)}`;
                group.appendChild(option);
            });

            vehicleInput.appendChild(group);
        }

        updateVehicleHint();
        initContactSelect2(vehicleInput, 'Busca vehículo o tipo');
    }

    btn.addEventListener('click', async () => {
        const error = validate();
        if (error) {
            showError(error);
            return;
        }

        btn.disabled = true;
        btn.textContent = 'Enviando...';

        const message = buildWhatsappMessage();
        const waTargetPhone = normalizeWaPhone(waPhone || telefonoInput.value);
        const waUrl = waTargetPhone ? `https://wa.me/${waTargetPhone}?text=${message}` : '';

        let waWindow = null;
        if (waUrl) {
            waWindow = window.open('about:blank', '_blank');
        }

        try {
            const itemId = Number(servicioInput.value);
            const vehicleContext = getSelectedVehicleContext();
            const response = await fetch(@json(route('reservas.catalogo')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    item_id: itemId,
                    item_type: 'catalog',
                    vehicle_id: vehicleContext.vehicleId,
                    vehicle_type_id: vehicleContext.vehicleTypeId,
                    vehicle_specification_id: vehicleContext.vehicleSpecificationId,
                })
            });

            const data = await response.json().catch(() => ({}));

            if (waWindow && waUrl) {
                waWindow.location.href = waUrl;
            } else if (waUrl) {
                window.location.href = waUrl;
            }

            if (!response.ok) {
                showError((data && data.message ? data.message : 'No se pudo crear la reserva.') + ' WhatsApp se abrió correctamente.');
            } else {
                showSuccess('Reserva creada exitosamente. Te contactaremos pronto.');
                form.reset();
                renderVehicleOptions(null);
            }
        } catch (e) {
            if (waWindow && waUrl) {
                waWindow.location.href = waUrl;
            } else if (waUrl) {
                window.location.href = waUrl;
            }
            showError('No se pudo registrar la reserva, pero WhatsApp se abrió para continuar.');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Reservar Ahora';
        }
    });

    fechaInput.addEventListener('change', () => {
        if (fechaInput.value && isSunday(fechaInput.value)) {
            fechaInput.value = '';
            showError('No se agendan reservas en domingo.');
        }
    });

    servicioInput.addEventListener('change', () => {
        renderVehicleOptions(getSelectedItem());
    });

    vehicleInput?.addEventListener('change', updateVehicleHint);
    initContactSelect2(servicioInput, 'Busca servicio');
})();
</script>
@endpush
