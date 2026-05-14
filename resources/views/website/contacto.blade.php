<section id="contacto" class="section section-dark contact-section">
    @php
        $reservableItems = \App\Models\CatalogItem::with(['type', 'category'])
            ->where('active', true)
            ->where('reservable', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $waUrl = $empresa->whatsapp_url ?? '#';
    @endphp

    <div class="section-header fade-up">
        <h2 class="section-title">Contactanos</h2>
        <p class="section-sub">Estamos aqui para atenderte. Reserva tu cita o consulta lo que necesites.</p>
    </div>

    <div class="contact-layout fade-up">
        <aside class="contact-info-col">
            <h3 class="contact-block-title">Nuestra Informacion de Contacto</h3>

            <div class="contact-list">
                <div class="contact-row">
                    <div class="contact-bullet whatsapp" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" class="contact-icon" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 11.5C20 16.1944 16.1944 20 11.5 20C10.1227 20 8.8219 19.6725 7.67138 19.0916L4 20L4.94782 16.4705C4.33863 15.3343 4 14.0356 4 12.6579C4 7.96355 7.80558 4.15796 12.5 4.15796C17.1944 4.15796 21 7.96355 21 12.6579" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9.26953 9.18066C9.51604 8.95114 9.74158 8.95114 9.90865 9.18066L11.1302 10.859C11.2599 11.0373 11.2599 11.2872 11.1302 11.4655L10.587 12.2116C10.4878 12.3478 10.4685 12.5293 10.5365 12.6835C10.8954 13.4972 11.5028 14.1046 12.3165 14.4635C12.4707 14.5315 12.6522 14.5122 12.7884 14.413L13.5345 13.8698C13.7128 13.7401 13.9627 13.7401 14.141 13.8698L15.8193 15.0914C16.0489 15.2584 16.0489 15.484 15.8193 15.7305L15.3334 16.2524C14.8914 16.7274 14.2005 16.8898 13.5993 16.6654C10.8744 15.6487 8.35133 13.1256 7.33463 10.4007C7.11023 9.79953 7.27256 9.10856 7.74758 8.66661L8.26953 8.18066H9.26953Z" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="warn">WhatsApp</h4>
                        <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer">{{ $empresa->telefono_contacto }}</a>
                        <p>Respuesta inmediata</p>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-bullet location" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" class="contact-icon" xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 21C12 21 18 15.6274 18 10.5C18 7.18629 15.3137 4.5 12 4.5C8.68629 4.5 6 7.18629 6 10.5C6 15.6274 12 21 12 21Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="10.5" r="2.25" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="warn">Ubicacion</h4>
                        <p>{{ $empresa->direccion_completa }}</p>
                        <p>{{ $empresa->ciudad_texto }}</p>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-bullet schedule" aria-hidden="true">
                        <svg viewBox="0 0 24 24" fill="none" class="contact-icon" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="12" cy="12" r="8" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M12 8V12L14.75 14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="warn">Horarios</h4>
                        <p>{{ $empresa->horario_texto }}</p>
                        <p class="warn">Domingos (Mantenimiento)</p>
                    </div>
                </div>
            </div>

            <div class="map-wrap">
                <div class="map-head">
                    <h3>Encuentranos</h3>
                    <p>Guiate por Google Maps para llegar a nuestra ubicacion.</p>
                </div>
                <div class="map-frame">
                    <iframe
                        src="{{ $empresa->ubicacion_mapa_url }}"
                        allowfullscreen
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </aside>

        <section class="contact-form-card">
            <h3>Envianos un Mensaje</h3>

            <form id="contact-booking-form" class="contact-form-grid" novalidate>
                <div>
                    <label for="contact-nombre">Nombre Completo <span>*</span></label>
                    <input type="text" id="contact-nombre" required minlength="3" placeholder="Tu nombre completo">
                </div>

                <div>
                    <label for="contact-telefono">Telefono de Contacto <span>*</span></label>
                    <input type="tel" id="contact-telefono" required pattern="[0-9+\s()-]{10,15}" placeholder="+593 98 123 4546">
                </div>

                <div>
                    <label for="contact-servicio">Item que deseas agendar <span>*</span></label>
                    <select id="contact-servicio" class="contact-service-select" required>
                        <option value="">Selecciona un item</option>
                        @foreach($reservableItems as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->type->name ?? 'Catalogo' }} / {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
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
                            @for($h = 8; $h <= 17; $h++)
                                @php($hour = str_pad((string)$h, 2, '0', STR_PAD_LEFT).':00')
                                <option value="{{ $hour }}">{{ $hour }}</option>
                            @endfor
                        </select>
                        <p class="field-hint">08:00 am - 18:00 pm</p>
                    </div>
                </div>

                <div>
                    <label for="contact-mensaje">Comentarios adicionales <small>(opcional)</small></label>
                    <textarea id="contact-mensaje" rows="3" maxlength="500" placeholder="Ej: Necesito una mesa para 4, una camioneta, un combo o una reserva especial..."></textarea>
                    <p class="field-hint">Escribenos tu mensaje describiendo todo lo que necesites.</p>
                </div>

                <button type="button" id="contact-submit" class="contact-submit-btn">Reservar Ahora</button>
            </form>
        </section>
    </div>
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
    const horaInput = document.getElementById('contact-hora');
    const mensajeInput = document.getElementById('contact-mensaje');
    const waPhone = @json((string) ($empresa->telefono_contacto ?? ''));

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
        if (!nombreInput.value || nombreInput.value.trim().length < 3) return 'Ingresa tu nombre completo (minimo 3 caracteres).';
        if (!telefonoInput.value.trim()) return 'Ingresa tu telefono de contacto.';
        if (!servicioInput.value) return 'Selecciona un item del catalogo.';
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
        const itemName = servicioInput.options[servicioInput.selectedIndex]?.text || 'Item';
        let msg = `NUEVA CITA\n\n`;
        msg += `Nombre: ${nombreInput.value.trim()}\n`;
        msg += `Telefono: ${telefonoInput.value.trim()}\n`;
        msg += `Item: ${itemName}\n`;
        msg += `Fecha: ${formatDate(fechaInput.value)}\n`;
        msg += `Hora: ${horaInput.value}\n`;
        if (mensajeInput.value.trim()) msg += `\nComentarios:\n${mensajeInput.value.trim()}`;
        return encodeURIComponent(msg);
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
            const response = await fetch(@json(route('reservas.catalogo')), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ item_id: itemId, item_type: 'catalog' })
            });

            const data = await response.json().catch(() => ({}));

            if (waWindow && waUrl) {
                waWindow.location.href = waUrl;
            } else if (waUrl) {
                window.location.href = waUrl;
            }

            if (!response.ok) {
                showError((data && data.message ? data.message : 'No se pudo crear la reserva.') + ' WhatsApp se abrio correctamente.');
            } else {
                showSuccess('Reserva creada exitosamente. Te contactaremos pronto.');
                form.reset();
            }
        } catch (e) {
            if (waWindow && waUrl) {
                waWindow.location.href = waUrl;
            } else if (waUrl) {
                window.location.href = waUrl;
            }
            showError('No se pudo registrar la reserva, pero WhatsApp se abrio para continuar.');
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
})();
</script>
@endpush
