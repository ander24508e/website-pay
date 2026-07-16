<div class="card-section">
    <h3 class="security-title">Seguridad y Contrasena</h3>
    <div class="section-label">
        <x-heroicon-o-shield-check class="w-4 h-4" />
        Cambiar Contrasena
        <span class="section-optional">
            Opcional
        </span>
    </div>
    <div class="fields-grid fields-grid--security">
        <div class="form-group">
            <label class="form-label">Contrasena Actual</label>
            <div class="input-wrap">
                <input type="password" id="cur_pass" name="current_password" class="form-input" placeholder="Contrasena actual" autocomplete="current-password" required>
                <button type="button" class="eye-btn" onclick="togglePass('cur_pass', this)" aria-label="Mostrar contrasena actual" aria-pressed="false">
                    <span class="eye-icon eye-icon--hidden">
                        <x-heroicon-o-eye-slash class="w-4 h-4" />
                    </span>
                    <span class="eye-icon eye-icon--visible">
                        <x-heroicon-o-eye class="w-4 h-4" />
                    </span>
                </button>
            </div>
            @error('current_password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Nueva Contrasena</label>
            <div class="input-wrap">
                <input type="password" id="new_pass" name="password" class="form-input" placeholder="Minimo 8 caracteres" autocomplete="new-password" required>
                <button type="button" class="eye-btn" onclick="togglePass('new_pass', this)" aria-label="Mostrar nueva contrasena" aria-pressed="false">
                    <span class="eye-icon eye-icon--hidden">
                        <x-heroicon-o-eye-slash class="w-4 h-4" />
                    </span>
                    <span class="eye-icon eye-icon--visible">
                        <x-heroicon-o-eye class="w-4 h-4" />
                    </span>
                </button>
            </div>
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirmar Contrasena</label>
            <div class="input-wrap">
                <input type="password" id="conf_pass" name="password_confirmation" class="form-input" placeholder="Repite la nueva contrasena" autocomplete="new-password" required>
                <button type="button" class="eye-btn" onclick="togglePass('conf_pass', this)" aria-label="Mostrar confirmacion de contrasena" aria-pressed="false">
                    <span class="eye-icon eye-icon--hidden">
                        <x-heroicon-o-eye-slash class="w-4 h-4" />
                    </span>
                    <span class="eye-icon eye-icon--visible">
                        <x-heroicon-o-eye class="w-4 h-4" />
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
