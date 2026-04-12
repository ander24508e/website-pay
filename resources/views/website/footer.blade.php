
<footer class="footer">
    <div class="footer-grid">
        <div>
            <div class="footer-brand">
                {{ strtoupper($empresa->nombre ?? 'Endara Carwash') }}
            </div>
            <p class="footer-desc">Tu vehículo en las mejores manos. Servicio profesional, productos de calidad y atención personalizada en Cayambe.</p>
        </div>
        <div>
            <div class="footer-title">Servicios</div>
            <ul class="footer-links">
                <li><a href="#servicios">Lavada Completa</a></li>
                <li><a href="#servicios">Lavada Express</a></li>
                <li><a href="#servicios">Lavada Premium</a></li>
                <li><a href="#servicios">Lubricación de Motor</a></li>
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
        <p class="footer-copy">Hecho con ❤️ en Cayambe, Ecuador</p>
    </div>
</footer>