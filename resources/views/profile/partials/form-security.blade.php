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
                <input type="password" id="cur_pass" name="current_password" class="form-input" placeholder="Contrasena actual">
                <button type="button" class="eye-btn" onclick="togglePass('cur_pass')">
                    <x-heroicon-o-eye-slash class="w-4 h-4" />
                </button>
            </div>
            @error('current_password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Nueva Contrasena</label>
            <div class="input-wrap">
                <input type="password" id="new_pass" name="password" class="form-input" placeholder="Minimo 8 caracteres">
                <button type="button" class="eye-btn" onclick="togglePass('new_pass')">
                    <x-heroicon-o-eye-slash class="w-4 h-4" />
                </button>
            </div>
            @error('password')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Confirmar Contrasena</label>
            <div class="input-wrap">
                <input type="password" id="conf_pass" name="password_confirmation" class="form-input" placeholder="Repite la nueva contrasena">
                <button type="button" class="eye-btn" onclick="togglePass('conf_pass')">
                    <x-heroicon-o-eye-slash class="w-4 h-4" />
                </button>
            </div>
        </div>
    </div>
</div>
