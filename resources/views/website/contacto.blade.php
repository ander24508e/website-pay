<section class="section section-dark" id="contacto">
    <div class="section-header fade-up">
        <div class="section-tag">Encuentranos</div>
        <h2 class="section-title">CONTACTO Y <span>UBICACION</span></h2>
        <div class="divider"></div>
    </div>

    <div class="contact-grid fade-up">
        <div>
            <div class="contact-item">
                <div class="contact-icon">📍</div>
                <div class="contact-text">
                    <h4>Direccion</h4>
                    <p>{{ $empresa->direccion_completa }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-text">
                    <h4>Telefono</h4>
                    <p>{{ $empresa->telefono_contacto }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">✉️</div>
                <div class="contact-text">
                    <h4>Correo</h4>
                    <p>{{ $empresa->correo_contacto }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">⏰</div>
                <div class="contact-text">
                    <h4>Horario</h4>
                    <p>{{ $empresa->horario_texto }}</p>
                </div>
            </div>
        </div>
        <div class="map-container">
            <iframe
                src="{{ $empresa->ubicacion_mapa_url }}"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>
