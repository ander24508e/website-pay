<footer class="footer footer-modern">
    <div class="footer-wrap">
        <div class="footer-grid">
            <div class="footer-col footer-col-brand">
                <div class="footer-brand-row">
                    <span class="footer-brand-badge">?</span>
                    <strong>{{ $empresa->nombre ?? 'Lavadora Endara' }}</strong>
                </div>
                <p class="footer-desc">{{ $empresa->descripcion_footer_texto }}</p>
                <div class="footer-social">
                    <a href="#" aria-label="Facebook">f</a>
                    <a href="#" aria-label="Instagram">?</a>
                    <a href="https://wa.me/{{ preg_replace('/\D+/', '', (string) $empresa->telefono_contacto) }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp">?</a>
                </div>
            </div>

            <div class="footer-col">
                <h4>Horarios</h4>
                <ul class="footer-list">
                    <li><span>?</span><div><strong>Lun - Vie</strong><small>{{ $empresa->horario_texto }}</small></div></li>
                    <li><span>?</span><div><strong>Sábado</strong><small>{{ $empresa->horario_texto }}</small></div></li>
                    <li><span>?</span><div><strong>Domingo</strong><small>Domingos (Mantenimiento)</small></div></li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <ul class="footer-list">
                    <li><span>?</span><a href="tel:{{ $empresa->telefono_contacto }}">{{ $empresa->telefono_contacto }}</a></li>
                    <li><span>?</span><a href="mailto:{{ $empresa->correo_contacto }}">{{ $empresa->correo_contacto }}</a></li>
                    <li><span>?</span><a href="#contacto">{{ $empresa->direccion_completa }}</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© {{ date('Y') }} <span>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</span>. Todos los derechos reservados.</p>
            <p>Politica de Privacidad | Politica de Cookies</p>
        </div>
    </div>
</footer>
