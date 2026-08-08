<section id="contacto" class="section section-dark contact-section">
    @php
        $reservableItems = collect($contactReservableItems ?? []);
        $waUrl = $empresa->whatsapp_url ?? '#';
    @endphp

    <div class="section-header fade-up">
        <h2 class="section-title">Contáctanos</h2>
        <p class="section-sub">Estamos aquí para atenderte. Reserva tu cita o consulta lo que necesites.</p>
    </div>

    <div class="contact-layout fade-up">
        @include('website.contacto.informacion')
        @include('website.contacto.formulario')
    </div>
</section>
