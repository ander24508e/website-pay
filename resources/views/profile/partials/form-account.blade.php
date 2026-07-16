<div class="card-section">
    <h3 class="security-title">Cuenta</h3>
    <div class="section-label">
        <x-heroicon-o-user class="w-4 h-4" />
        Informacion Personal
    </div>
    <div class="fields-grid">
        <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" placeholder="Tu nombre" required autocomplete="name">
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Correo Electronico</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" placeholder="tu@email.com" required autocomplete="email">
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Telefono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $user->telefono) }}" class="form-input" placeholder="+593 98 123 4567" required autocomplete="tel">
            @error('telefono')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Direccion (Opcional)</label>
            <input type="text" name="direccion" value="{{ old('direccion', $user->direccion) }}" class="form-input" placeholder="Tu direccion" autocomplete="street-address">
            @error('direccion')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
