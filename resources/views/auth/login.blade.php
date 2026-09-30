<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-bold text-on-surface">Iniciar sesión</h1>
        <p class="mt-1 text-sm text-on-surface-variant">Accede a la mesa de ayuda con tu cuenta.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <a href="{{ route('auth.microsoft.redirect') }}" class="btn-secondary w-full">
        <svg width="16" height="16" viewBox="0 0 21 21"><rect x="1" y="1" width="9" height="9" fill="#f25022"/><rect x="11" y="1" width="9" height="9" fill="#7fba00"/><rect x="1" y="11" width="9" height="9" fill="#00a4ef"/><rect x="11" y="11" width="9" height="9" fill="#ffb900"/></svg>
        Iniciar sesión con Microsoft
    </a>

    <div class="my-5 flex items-center gap-3 text-xs text-outline">
        <div class="h-px flex-1 bg-outline-variant/40"></div>
        o con tu correo
        <div class="h-px flex-1 bg-outline-variant/40"></div>
    </div>

    <form method="POST" action="{{ route('login') }}" class="flex flex-col gap-4">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" class="mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between gap-2">
                <x-input-label for="password" value="Contraseña" />
                @if (Route::has('password.request'))
                    <a class="text-xs text-primary hover:underline" href="{{ route('password.request') }}">
                        ¿Olvidaste tu contraseña?
                    </a>
                @endif
            </div>
            <x-text-input id="password" class="mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
            <input id="remember_me" type="checkbox" class="form-check" name="remember">
            <span class="text-sm text-on-surface-variant">Recordarme</span>
        </label>

        <x-primary-button class="w-full">
            Iniciar sesión
        </x-primary-button>
    </form>
</x-guest-layout>
