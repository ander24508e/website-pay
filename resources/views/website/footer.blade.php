<footer class="footer">
    <div class="footer-grid">
        <div>
            <div class="footer-brand">
                {{ strtoupper($empresa->nombre ?? 'Endara Carwash') }}
            </div>
            <p class="footer-desc">{{ $empresa->descripcion_footer_texto }}</p>
        </div>
        <div>
            <div class="footer-title">Contacto</div>
            <ul class="footer-links">
                <li><a href="tel:{{ $empresa->telefono_contacto }}">{{ $empresa->telefono_contacto }}</a></li>
                <li><a href="mailto:{{ $empresa->correo_contacto }}">{{ $empresa->correo_contacto }}</a></li>
                <li><a href="#contacto">{{ $empresa->ciudad_texto }}</a></li>
                <li><a href="#contacto">{{ $empresa->horario_texto }}</a></li>
            </ul>
        </div>
        <div>
            <div class="footer-title">Accesos</div>
            <ul class="footer-links">
                <li><a href="#inicio">Inicio</a></li>
                <li><a href="#servicios">Servicios</a></li>
                <li><a href="#productos">Productos</a></li>
                <li><a href="#contacto">Contacto</a></li>
                @guest
                    <li><a href="{{ route('login') }}">Administrador</a></li>
                @endguest
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p class="footer-copy">© {{ date('Y') }} <span>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</span>. Todos los derechos reservados.</p>
        <p class="footer-copy">{{ $empresa->servicios_resumen_texto }}</p>
    </div>
</footer>
