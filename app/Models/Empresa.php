<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Empresa extends Model
{
    protected $fillable = [
        'nombre',
        'direccion',
        'telefono',
        'correo',
        'eslogan',
        'descripcion_corta',
        'descripcion_footer',
        'horario',
        'servicios_resumen',
        'ubicacion_embed',
        'ciudad',
        'facebook_url',
        'instagram_url',
        'tiktok_url',
        'whatsapp_url',
        'logo',
        'color_primario',
        'color_secundario',
        'color_terciario',
    ];

    public function landingBanners()
    {
        return $this->hasMany(LandingBanner::class)->ordered();
    }

    public function catalogTypes()
    {
        return $this->hasMany(CatalogType::class)->orderBy('sort_order')->orderBy('name');
    }

    public function catalogCategories()
    {
        return $this->hasMany(CatalogCategory::class)->orderBy('sort_order')->orderBy('name');
    }

    public function catalogItems()
    {
        return $this->hasMany(CatalogItem::class)->orderBy('sort_order')->orderBy('name');
    }

    public function getLogoUrlAttribute(): string
    {
        if ($this->logo && Storage::disk('public')->exists($this->logo)) {
            return Storage::url($this->logo);
        }

        return asset('Images/empresa-logo.jpg');
    }

    public function getNombreCortoAttribute(): string
    {
        return $this->nombre ?: 'Mi negocio';
    }

    public function getCorreoContactoAttribute(): string
    {
        return $this->correo ?: 'contacto@negocio.com';
    }

    public function getTelefonoContactoAttribute(): string
    {
        return $this->telefono ?: '+593 99 999 9999';
    }

    public function getDireccionCompletaAttribute(): string
    {
        return $this->direccion ?: ($this->ciudad ?: 'Cayambe, Ecuador');
    }

    public function getEsloganTextoAttribute(): string
    {
        return $this->eslogan ?: 'Servicio profesional para el cuidado de tu vehiculo';
    }

    public function getDescripcionCortaTextoAttribute(): string
    {
        return $this->descripcion_corta ?: 'Comparte aqui un texto breve para presentar tu negocio en la portada.';
    }

    public function getDescripcionFooterTextoAttribute(): string
    {
        return $this->descripcion_footer ?: 'Tu negocio, tus servicios y tu identidad en un solo lugar.';
    }

    public function getHorarioTextoAttribute(): string
    {
        return $this->horario ?: 'Lunes a Sabado: 08:00 - 18:00';
    }

    public function getServiciosResumenTextoAttribute(): string
    {
        return $this->servicios_resumen ?: 'Lavado - Lubricacion - Mantenimiento';
    }

    public function getCiudadTextoAttribute(): string
    {
        return $this->ciudad ?: 'Cayambe, Ecuador';
    }

    public function getFacebookUrlAttribute($value): ?string
    {
        return $this->normalizeExternalUrl($value);
    }

    public function getInstagramUrlAttribute($value): ?string
    {
        return $this->normalizeExternalUrl($value);
    }

    public function getTiktokUrlAttribute($value): ?string
    {
        return $this->normalizeExternalUrl($value);
    }

    public function getWhatsappUrlAttribute($value): ?string
    {
        $value = $this->normalizeExternalUrl($value);

        if ($value) {
            return $value;
        }

        $digits = preg_replace('/\D+/', '', (string) $this->telefono_contacto);

        if (!$digits) {
            return null;
        }

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode('¡Hola me gustaria mas informacion!');
    }

    public function getUbicacionMapaUrlAttribute(): string
    {
        $stored = trim((string) $this->ubicacion_embed);

        if ($stored !== '') {
            return $this->extractMapSrc($stored);
        }

        return 'https://www.google.com/maps?q=' . urlencode($this->direccion_completa) . '&output=embed';
    }

    public function getUbicacionEmbedUrlAttribute(): string
    {
        return $this->ubicacion_mapa_url;
    }

    public function getColorPrimarioHexAttribute(): string
    {
        return $this->normalizeHexColor($this->color_primario, '#D82128');
    }

    public function getColorSecundarioHexAttribute(): string
    {
        return $this->normalizeHexColor($this->color_secundario, '#F0B429');
    }

    public function getColorTerciarioHexAttribute(): string
    {
        return $this->normalizeHexColor($this->color_terciario, '#FFFFFF');
    }

    private function extractMapSrc(string $value): string
    {
        $decoded = html_entity_decode(trim($value), ENT_QUOTES, 'UTF-8');

        if (Str::contains($decoded, '<iframe') && preg_match('/src=["\']([^"\']+)["\']/i', $decoded, $matches)) {
            return $matches[1];
        }

        if (Str::contains($decoded, ['google.com/maps/embed', 'google.com/maps?q=', 'output=embed'])) {
            return $decoded;
        }

        return 'https://www.google.com/maps?q=' . urlencode($decoded) . '&output=embed';
    }

    private function normalizeHexColor(?string $value, string $fallback): string
    {
        $value = strtoupper(trim((string) $value));

        if (preg_match('/^#[0-9A-F]{6}$/', $value)) {
            return $value;
        }

        return $fallback;
    }

    private function normalizeExternalUrl(?string $value): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        if (!Str::startsWith($value, ['http://', 'https://'])) {
            $value = 'https://' . $value;
        }

        return $value;
    }
}
