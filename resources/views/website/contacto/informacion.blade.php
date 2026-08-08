<aside class="contact-info-col">
    <h3 class="contact-block-title">Nuestra Información de Contacto</h3>

    <div class="contact-list">
        <div class="contact-row">
            <div class="contact-bullet whatsapp" aria-hidden="true">
                <span class="contact-emoji">💬</span>
            </div>
            <div>
                <h4 class="warn">WhatsApp</h4>
                <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer">{{ $empresa->telefono_contacto }}</a>
                <p>Respuesta inmediata</p>
            </div>
        </div>

        <div class="contact-row">
            <div class="contact-bullet location" aria-hidden="true">
                <span class="contact-emoji">📍</span>
            </div>
            <div>
                <h4 class="warn">Ubicación</h4>
                <p>{{ $empresa->direccion_completa }}</p>
                <p>{{ $empresa->ciudad_texto }}</p>
            </div>
        </div>

        <div class="contact-row">
            <div class="contact-bullet schedule" aria-hidden="true">
                <span class="contact-emoji">🗓️</span>
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
            <h3>Encuéntranos</h3>
            <p>Guíate por Google Maps para llegar a nuestra ubicación.</p>
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
