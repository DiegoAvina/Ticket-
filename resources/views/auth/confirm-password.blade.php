<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-bold text-on-surface">Confirma tu contraseña</h1>
        <p class="mt-1 text-sm text-on-surface-variant">
            Esta es un área segura de la aplicación. Confirma tu contraseña antes de continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Contraseña" />
            <x-text-input id="password" class="mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">
            Confirmar
        </x-primary-button>
    </form>
</x-guest-layout>
