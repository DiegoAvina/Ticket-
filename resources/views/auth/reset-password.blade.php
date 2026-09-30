<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-bold text-on-surface">Restablecer contraseña</h1>
        <p class="mt-1 text-sm text-on-surface-variant">Elige una nueva contraseña para tu cuenta.</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" value="Nueva contraseña" />
            <x-text-input id="password" class="mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
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
            Restablecer contraseña
        </x-primary-button>
    </form>
</x-guest-layout>
