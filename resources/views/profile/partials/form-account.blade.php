<div class="card-section">
    <div class="section-label">
        <x-heroicon-o-user class="w-4 h-4" />
        Informacion Personal
    </div>
    <div class="fields-grid">
        <div class="form-group">
            <label class="form-label">Nombre</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" placeholder="Tu nombre" required>
            @error('name')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            <label class="form-label">Correo Electronico</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" placeholder="tu@email.com" required>
            @error('email')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
