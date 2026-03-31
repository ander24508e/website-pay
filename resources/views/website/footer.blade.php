<style>
.footer { background:var(--dark-3); border-top:1px solid rgba(255,255,255,0.06); padding:3rem 2rem 2rem; }
.footer-grid { display:grid; grid-template-columns:2fr 1fr 1fr; gap:3rem; max-width:1100px; margin:0 auto; }
.footer-brand { font-family:'Bebas Neue',sans-serif; font-size:1.4rem; color:white; margin-bottom:0.75rem; }
.footer-brand span { color:var(--red); }
.footer-desc { font-size:0.82rem; color:var(--muted); line-height:1.7; max-width:280px; }
.footer-title { font-size:0.7rem; font-weight:700; letter-spacing:0.15em; text-transform:uppercase; color:var(--gold); margin-bottom:1rem; }
.footer-links { list-style:none; display:flex; flex-direction:column; gap:0.6rem; }
.footer-links a { font-size:0.82rem; color:var(--muted); text-decoration:none; transition:color 0.2s; }
.footer-links a:hover { color:white; }
.footer-bottom { max-width:1100px; margin:2.5rem auto 0; padding-top:1.5rem; border-top:1px solid rgba(255,255,255,0.05); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:1rem; }
.footer-copy { font-size:0.75rem; color:var(--muted); }
.footer-copy span { color:var(--red); }

@media (max-width: 768px) {
    .footer { padding:2.5rem 1.25rem 1.5rem; }
    .footer-grid {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    .footer-desc { max-width:100%; }
    .footer-bottom {
        flex-direction: column;
        text-align: center;
        gap: 0.5rem;
    }
}
</style>

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