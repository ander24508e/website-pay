<div class="card-section">
    <div class="section-label">
        <x-heroicon-o-photo class="w-4 h-4" />
        Foto de Perfil
    </div>
    <div class="avatar-row">
        <img src="{{ $user->foto_perfil_url }}" id="avatar-preview" alt="Foto de perfil" class="avatar-img">
        <div class="avatar-info">
            <div class="avatar-name">{{ $user->name }}</div>
            <div class="avatar-email">{{ $user->email }}</div>

            <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" class="u-hidden-input" onchange="previewAvatar(this)">
            <button type="button" class="btn-upload" onclick="document.getElementById('foto_perfil').click()">
                <x-heroicon-o-cloud-arrow-up class="w-4 h-4" />
                Cambiar Foto
            </button>
            <p id="file-name-display" class="upload-hint"></p>
            <p class="upload-hint">JPG, PNG MAXIMO 4MB</p>
            @error('foto_perfil')
                <span class="field-error">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>
