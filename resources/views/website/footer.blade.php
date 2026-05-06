@php
    $waUrl = $empresa->whatsapp_url ?? '#';
    $footerNombre = strtoupper($empresa->nombre ?? 'Lavadora Endara');
    $footerPartes = explode(' ', $footerNombre);
    $footerInicio = implode(' ', array_slice($footerPartes, 0, 2));
    $footerDestacado = implode(' ', array_slice($footerPartes, 2));
@endphp

<footer class="footer footer-modern">
    <div class="footer-wrap">
        <div class="footer-grid">
            <div class="footer-col footer-col-brand">
                <div class="footer-brand-row">
                    <span class="footer-brand-badge">?</span>
                    <strong class="footer-brand-name">
                        {{ $footerInicio ?: $footerNombre }}
                        @if ($footerDestacado)
                            <span>{{ $footerDestacado }}</span>
                        @endif
                    </strong>
                </div>

                <p class="footer-desc">{{ $empresa->descripcion_footer_texto }}</p>
                <div class="footer-social">
                    @if($empresa->facebook_url)
                    <a href="{{ $empresa->facebook_url }}" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="text-gray-600 hover:text-blue-600 transition-colors">
                        <x-bi-facebook class="w-5 h-5" />
                    </a>
                    @endif
                    @if($empresa->instagram_url)
                    <a href="{{ $empresa->instagram_url }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"
                        class="text-gray-600 hover:text-pink-600 transition-colors">
                        <x-bi-instagram class="w-5 h-5" />
                    </a>
                    @endif
                    @if($empresa->tiktok_url)
                    <a href="{{ $empresa->tiktok_url }}" target="_blank" rel="noopener noreferrer" aria-label="TikTok" class="text-gray-600 hover:text-black transition-colors">
                        <x-bi-tiktok class="w-5 h-5" />
                    </a>
                    @endif
                </div>
            </div>

            <div class="footer-col">
                <h4>Horarios</h4>
                <ul class="footer-list">
                    <li><span></span>
                        <div><strong>Lun - Vie</strong><small>{{ $empresa->horario_texto }}</small></div>
                    </li>
                    <li><span></span>
                        <div><strong>Sabado</strong><small>{{ $empresa->horario_texto }}</small></div>
                    </li>
                    <li><span></span>
                        <div><strong>Domingo</strong><small>Domingos (Mantenimiento)</small></div>
                    </li>
                </ul>
            </div>

            <div class="footer-col">
                <h4>Contacto</h4>
                <ul class="footer-list">
                    <li><span></span><a href="{{ $waUrl }}" target="_blank"
                            rel="noopener noreferrer">{{ $empresa->telefono_contacto }}</a></li>
                    <li><span></span><a
                            href="mailto:{{ $empresa->correo_contacto }}">{{ $empresa->correo_contacto }}</a></li>
                    <li><span></span><a href="#contacto">{{ $empresa->direccion_completa }}</a></li>
                </ul>
            </div>
        </div>

        <div class="footer-bottom">
            <p>{{ date('Y') }} <span>{{ $empresa->nombre ?? 'Lavadora y Lubricadora Endara' }}</span>. Todos los
                derechos reservados.</p>
            <p>Politica de Privacidad | Politica de Cookies</p>
        </div>
        
    </div>
</footer>