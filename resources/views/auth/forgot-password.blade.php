<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-bold text-on-surface">Recuperar contraseña</h1>
        <p class="mt-1 text-sm text-on-surface-variant">
            ¿Olvidaste tu contraseña? No hay problema. Indícanos tu correo electrónico y te enviaremos un enlace para que elijas una nueva.
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Enviar enlace de restablecimiento
        </x-primary-button>

        <p class="text-center text-sm">
            <a class="text-primary hover:underline" href="{{ route('login') }}">Volver a iniciar sesión</a>
        </p>
    </form>
</x-guest-layout>
