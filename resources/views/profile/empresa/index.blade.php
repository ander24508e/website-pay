@extends('menu')

@section('contenido')
    @vite(['resources/scss/empresa.scss'])

    {{-- Notificación de éxito --}}
    @if (session('status') === 'empresa-updated')
        <div class="notification-success">
            <div class="notification-content">
                <i class="bi bi-check-circle"></i>
                <span>{{ __('Información actualizada correctamente.') }}</span>
            </div>
            <button class="notification-close" onclick="this.parentElement.remove()">
                <i class="bi bi-x"></i>
            </button>
        </div>
    @endif

    <div class="app-container">
        <div class="app-wrapper">
            <div class="app-card">
                <div class="app-content">
                    <form method="post" action="{{ route('empresa.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('patch')

                        <!-- Sección Logo Extendida - Rediseñada según imagen -->
                        <div class="empresa-logo-section">
                            <div class="logo-preview-container"
                                style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                                <div class="logo-preview-wrapper"
                                    style="display: flex; flex-direction: column; align-items: center;">
                                    <img src="{{ $empresa->logo_url }}" class="empresa-logo-preview" alt="Logo actual"
                                        id="current-logo-preview"
                                        style="inline-size: 150px; block-size: 150px; object-fit: contain; border: 1px solid #e0e0e0; border-radius: 8px; padding: 1rem; margin-block-end: 0.5rem;">
                                    <span class="logo-current-text" style="font-size: 0.85rem; color: #666;">Logo
                                        actual</span>
                                </div>

                                <div class="logo-upload-section">
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-block-end: 0.5rem;">
                                        <span style="color: #555; font-size: 0.9rem;">Seleccionar archivo |</span>
                                        <span style="color: #d82128; font-size: 0.9rem;" id="file-name-display">
                                            @if($empresa->logo)
                                                {{ basename($empresa->logo) }}
                                            @else
                                                Ningún archivo seleccionado
                                            @endif
                                        </span>
                                    </div>

                                    <div class="modern-file-upload">
                                        <input type="file" id="logo" name="logo" accept="image/*"
                                            class="modern-file-input" onchange="previewLogo(this)" style="display: none;">
                                        <button type="button" onclick="document.getElementById('logo').click()"
                                            style="background: #d82128; color: white; border: none; padding: 0.75rem 2rem; border-radius: 50px; font-size: 1rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.5rem; box-shadow: 0 4px 15px rgba(216, 33, 40, 0.3); transition: all 0.3s ease;"
                                            onmouseover="this.style.background='#b41b21'"
                                            onmouseout="this.style.background='#d82128'">
                                            <i class="bi bi-cloud-arrow-up" style="font-size: 1.2rem;"></i>
                                            Cambiar Logo
                                        </button>
                                    </div>

                                    <p class="empresa-logo-hint"
                                        style="font-size: 0.8rem; color: #999; margin-block-start: 0.75rem;">
                                        <i class="bi bi-info-circle"></i>
                                        JPG, PNG, SVG (Max: 4MB)
                                    </p>

                                    @error('logo')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Sección Información --}}
                        <div class="app-section">
                            <h2 class="section-title">
                                <i class="bi bi-building"></i>
                                Información de la Empresa
                            </h2>

                            <div class="fields-grid">
                                <div class="form-group">
                                    <label for="nombre" class="form-label">Nombre de la Empresa</label>
                                    <input type="text" id="nombre" name="nombre"
                                        value="{{ old('nombre', $empresa->nombre) }}" class="form-input"
                                        placeholder="Nombre de tu empresa" required autofocus>
                                    @error('nombre')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="tel" id="telefono" name="telefono"
                                        value="{{ old('telefono', $empresa->telefono) }}" class="form-input"
                                        placeholder="Número de contacto">
                                    <small class="text-muted" style="font-size: 0.75rem; color: #999; margin-block-start: 0.25rem; display: block;">
                                       
                                    </small>
                                    @error('telefono')
                                        <span class="error-message">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="direccion" class="form-label">Dirección</label>
                                <textarea id="direccion" name="direccion" class="form-input" placeholder="Dirección completa de la empresa"
                                    rows="3">{{ old('direccion', $empresa->direccion) }}</textarea>
                                <small class="text-muted" style="font-size: 0.75rem; color: #999; margin-block-start: 0.25rem; display: block;">
                                
                                </small>
                                @error('direccion')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Botón Guardar --}}
                        <div class="save-section">
                            <button type="submit" class="btn-primary">
                                <i class="bi bi-check-lg"></i>
                                {{ __('Guardar Cambios') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    function previewLogo(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validar tamaño (4MB)
            const maxSize = 4 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('La imagen es muy grande. Máximo 4MB.');
                input.value = '';
                return;
            }
            
            // Validar tipo
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/svg+xml'];
            if (!validTypes.includes(file.type) && !file.name.match(/\.svg$/i)) {
                alert('Solo se permiten imágenes JPG, PNG y SVG.');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('current-logo-preview');
                preview.src = e.target.result;
                
                // Pequeña animación
                preview.style.opacity = '0.5';
                setTimeout(() => {
                    preview.style.opacity = '1';
                }, 100);
            }
            reader.readAsDataURL(file);
            
            // Actualizar nombre del archivo
            const fileNameDisplay = document.getElementById('file-name-display');
            if (fileNameDisplay) {
                fileNameDisplay.textContent = file.name;
                fileNameDisplay.style.color = '#d82128';
            }
        }
    }

    // Auto-ocultar notificación después de 4 segundos
    document.addEventListener('DOMContentLoaded', function() {
        const notification = document.querySelector('.notification-success');
        if (notification) {
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateY(-100%)';
                setTimeout(() => notification.remove(), 300);
            }, 4000);
        }

        // Actualizar el nombre del archivo cuando se selecciona uno
        const fileInput = document.getElementById('logo');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    previewLogo(this);
                }
            });
        }
    });
</script>