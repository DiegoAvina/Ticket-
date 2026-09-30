<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-bold text-on-surface">Crear cuenta</h1>
        <p class="mt-1 text-sm text-on-surface-variant">Regístrate para levantar y dar seguimiento a tus tickets.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" class="mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <x-input-label for="password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="password_confirmation" class="mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full mt-2">
            Registrarme
        </x-primary-button>

        <p class="text-center text-sm text-on-surface-variant">
            ¿Ya tienes cuenta?
            <a class="text-primary hover:underline" href="{{ route('login') }}">Inicia sesión</a>
        </p>
    </form>
</x-guest-layout>
