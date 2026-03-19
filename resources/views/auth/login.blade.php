<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @php($empresa = App\Models\Empresa::first())
    <title>Login - {{ $empresa->nombre ?? 'Sistema' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    @vite(['resources/scss/all.scss'])
</head>

<body class="min-h-screen flex items-center justify-center bg-white relative">

    <!-- Ventana flotante más compacta -->
    <div class="w-full max-w-sm bg-white bg-opacity-95 backdrop-blur-xl rounded-2xl shadow-2xl p-6">

        <!-- Logo -->
        <div class="text-center mb-3">
            <img src="{{ $empresa->logo_url ?? asset('Images/empresa-logo.jpg') }}" alt="{{ $empresa->nombre ?? 'Sistema' }}"
                class="w-24 h-24 mx-auto object-contain rounded-full shadow-md">
            <h1 class="text-base font-bold text-gray-700 mt-1">Iniciar Sesión</h1>
        </div>

        <!-- Formulario -->
        <form method="POST" action="{{ route('login') }}" class="space-y-4 mx-auto w-full max-w-xs">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Correo Electrónico</label>
                <input id="email" name="email" type="email"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg" placeholder="tu@email.com"
                    value="{{ old('email') }}" required autofocus>
                <x-input-error :messages="$errors->get('email')" class="text-red-500 text-xs mt-1" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Contraseña</label>

                <div class="relative">
                    <input id="password" name="password" type="password"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg" placeholder="••••••••" required>

                    <!-- Ícono ojo -->
                    <i class="bi bi-eye-slash absolute right-3 top-2.5 text-gray-600 cursor-pointer"
                        onclick="togglePassword('password', this)"></i>
                </div>
                <x-input-error :messages="$errors->get('password')" class="text-red-500 text-xs mt-1" />
            </div>

            <!-- Remember / Forgot -->
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="h-3.5 w-3.5 border-gray-300 rounded">
                    <span class="ml-1 text-gray-600">Recordarme</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-red-600 hover:text-red-800">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>

            <!-- Botón -->
            <button type="submit"
                class="w-full py-2 bg-red-600 text-white text-sm font-semibold rounded-lg shadow-md hover:bg-red-700 transition">
                Iniciar Sesión
            </button>
        </form>

    </div>

</body>

<script>
    function togglePassword(id, icon) {
        const input = document.getElementById(id);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        }
    }
</script>

</html>