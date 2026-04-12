
<section class="section section-dark" id="contacto">
    <div class="section-header fade-up">
        <div class="section-tag">Encuéntranos</div>
        <h2 class="section-title">CONTACTO Y <span>UBICACIÓN</span></h2>
        <div class="divider"></div>
    </div>

    <div class="contact-grid fade-up">
        <div>
            <div class="contact-item">
                <div class="contact-icon">📍</div>
                <div class="contact-text">
                    <h4>Dirección</h4>
                    <p>{{ $empresa->direccion ?? 'Cayambe, Pichincha, Ecuador' }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">📞</div>
                <div class="contact-text">
                    <h4>Teléfono</h4>
                    <p>{{ $empresa->telefono ?? '+593 99 999 9999' }}</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">⏰</div>
                <div class="contact-text">
                    <h4>Horario</h4>
                    <p>Lunes a Sábado: 8:00 — 18:00</p>
                </div>
            </div>
            <div class="contact-item">
                <div class="contact-icon">🚗</div>
                <div class="contact-text">
                    <h4>Servicios</h4>
                    <p>Lavado · Lubricación · Mantenimiento</p>
                </div>
            </div>
        </div>
        <div class="map-container">
            {{-- Reemplaza el src con tu ubicación real en Google Maps --}}
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.7!2d-78.14!3d0.04!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMMKwMDInMjQuMCJOIDc4wrAwOCczNi4wIlc!5e0!3m2!1ses!2sec!4v1"
                allowfullscreen="" loading="lazy">
            </iframe>
        </div>
    </div>
</section>