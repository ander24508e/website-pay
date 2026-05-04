<section id="contacto" class="section section-dark contact-section">
    @php
        $reservableServices = \App\Models\Service::with('category')
            ->where('active', true)
            ->get()
            ->filter(function ($service) {
                $text = strtolower(trim(($service->name ?? '') . ' ' . ($service->description ?? '') . ' ' . ($service->category->name ?? '')));
                return str_contains($text, 'lavad');
            })
            ->values();

        $waPhone = preg_replace('/\D+/', '', (string) $empresa->telefono_contacto);
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
                    <div class="contact-bullet whatsapp">W</div>
                    <div>
                        <h4>WhatsApp</h4>
                        <a href="{{ $waPhone ? 'https://wa.me/'.$waPhone : '#' }}" target="_blank" rel="noopener noreferrer">{{ $empresa->telefono_contacto }}</a>
                        <p>Respuesta inmediata</p>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-bullet location">U</div>
                    <div>
                        <h4>Ubicacion</h4>
                        <p>{{ $empresa->direccion_completa }}</p>
                        <p>{{ $empresa->ciudad_texto }}</p>
                    </div>
                </div>

                <div class="contact-row">
                    <div class="contact-bullet schedule">H</div>
                    <div>
                        <h4>Horarios</h4>
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
            <h3>Envíanos un Mensaje</h3>

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
                    <label for="contact-servicio">Servicio que deseas agendar <span>*</span></label>
                    <select id="contact-servicio" required>
                        <option value="">Selecciona un servicio</option>
                        @foreach($reservableServices as $service)
                            <option value="{{ $service->id }}">{{ $service->name }}</option>
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
                        <select id="contact-hora" required>
                            <option value="">Selecciona hora</option>
                            @for($h = 8; $h <= 17; $h++)
                                @php($hour = str_pad((string)$h, 2, '0', STR_PAD_LEFT).':00')
                                <option value="{{ $hour }}">{{ $hour }}</option>
                            @endfor
                        </select>
                        <p class="field-hint">08:00 - 18:00</p>
                    </div>
                </div>

                <div>
                    <label for="contact-mensaje">Comentarios adicionales <small>(opcional)</small></label>
                    <textarea id="contact-mensaje" rows="3" maxlength="500" placeholder="Ej: Tengo camioneta, necesito lavado completo..."></textarea>
                    <p class="field-hint">Escribenos tu mensaje describiendo todo lo que necesites.</p>
                </div>

                <div id="contact-error" class="contact-alert error hidden"></div>
                <div id="contact-success" class="contact-alert success hidden"></div>

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
    const errorBox = document.getElementById('contact-error');
    const successBox = document.getElementById('contact-success');
    const fechaInput = document.getElementById('contact-fecha');

    if (!form || !btn || !fechaInput) return;

    const nombreInput = document.getElementById('contact-nombre');
    const telefonoInput = document.getElementById('contact-telefono');
    const servicioInput = document.getElementById('contact-servicio');
    const horaInput = document.getElementById('contact-hora');
    const mensajeInput = document.getElementById('contact-mensaje');
    const waPhone = @json($waPhone ?: '');

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    fechaInput.setAttribute('min', `${yyyy}-${mm}-${dd}`);

    function showError(message) {
        errorBox.textContent = message;
        errorBox.classList.remove('hidden');
        successBox.classList.add('hidden');
    }

    function showSuccess(message) {
        successBox.textContent = message;
        successBox.classList.remove('hidden');
        errorBox.classList.add('hidden');
    }

    function isSunday(dateText) {
        const date = new Date(`${dateText}T00:00:00`);
        return date.getDay() === 0;
    }

    function validate() {
        if (!nombreInput.value || nombreInput.value.trim().length < 3) return 'Ingresa tu nombre completo (minimo 3 caracteres).';
        if (!telefonoInput.value.trim()) return 'Ingresa tu telefono de contacto.';
        if (!servicioInput.value) return 'Selecciona un servicio de lavado.';
        if (!fechaInput.value) return 'Selecciona una fecha.';
        if (isSunday(fechaInput.value)) return 'No se agendan reservas en domingo.';
        if (!horaInput.value) return 'Selecciona una hora.';
        return null;
    }

    function formatDate(dateText) {
        const [y,m,d] = dateText.split('-');
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
        const servicioName = servicioInput.options[servicioInput.selectedIndex]?.text || 'Servicio';
        let msg = `NUEVA CITA\n\n`;
        msg += `Nombre: ${nombreInput.value.trim()}\n`;
        msg += `Telefono: ${telefonoInput.value.trim()}\n`;
        msg += `Servicio: ${servicioName}\n`;
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
            const serviceId = Number(servicioInput.value);
            const response = await fetch(`/reservas/servicio/${serviceId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token()),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({})
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
