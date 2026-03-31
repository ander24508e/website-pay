<style>
.contact-grid { display:grid; grid-template-columns:1fr 1fr; gap:3rem; max-width:1100px; margin:0 auto; }
.contact-item { display:flex; gap:1rem; margin-bottom:2rem; align-items:flex-start; }
.contact-icon { width:48px; height:48px; border-radius:8px; background:rgba(216,33,40,0.12); border:1px solid rgba(216,33,40,0.25); display:flex; align-items:center; justify-content:center; font-size:1.2rem; flex-shrink:0; }
.contact-text h4 { font-size:0.7rem; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; color:var(--muted); margin-bottom:0.3rem; }
.contact-text p { font-size:0.95rem; color:white; font-weight:500; }
.map-container { border-radius:8px; overflow:hidden; border:1px solid rgba(255,255,255,0.06); height:350px; }
.map-container iframe { width:100%; height:100%; border:none; }

@media (max-width: 768px) {
    .contact-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    .map-container { height: 260px; }
    .contact-text p { font-size:0.85rem; }
}
</style>

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