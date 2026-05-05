@php
    $rawPhone = (string) ($empresa->telefono_contacto ?? '');
    $waPhone = preg_replace('/\D+/', '', $rawPhone);
    $waMessage = urlencode('¡Hola me gustaria obtener mas infromacion!');
    $waUrl = $waPhone ? "https://wa.me/{$waPhone}?text={$waMessage}" : null;
    $waBg = '#25D366';
    $waBadge = $empresa->color_primario_hex ?? '#D82128';
@endphp

@if($waUrl)
    <a
        href="{{ $waUrl }}"
        target="_blank"
        rel="noopener noreferrer"
        class="whatsapp-float-btn"
        aria-label="Contactar por WhatsApp"
    >
        <img src="https://upload.wikimedia.org/wikipedia/commons/6/6b/WhatsApp.svg" alt="WhatsApp" loading="lazy">
        <span>1</span>
    </a>

    <style>
        .whatsapp-float-btn {
            position: fixed;
            right: 1rem;
            bottom: 1rem;
            z-index: 1050;
            width: 60px;
            height: 60px;
            border-radius: 999px;
            background: {{ $waBg }};
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.35);
            transition: transform .2s ease, background-color .2s ease;
        }

        .whatsapp-float-btn:hover {
            transform: scale(1.08);
            filter: brightness(0.92);
        }

        .whatsapp-float-btn img {
            width: 34px;
            height: 34px;
        }

        .whatsapp-float-btn span {
            position: absolute;
            right: -2px;
            top: -2px;
            width: 22px;
            height: 22px;
            border-radius: 999px;
            background: {{ $waBadge }};
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        @media (max-width: 768px) {
            .whatsapp-float-btn {
                width: 56px;
                height: 56px;
                right: .75rem;
                bottom: .75rem;
            }
        }
    </style>
@endif
