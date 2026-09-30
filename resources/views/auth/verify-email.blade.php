<x-guest-layout>
    <div class="mb-6">
        <h1 class="font-display text-xl font-bold text-on-surface">Verifica tu correo</h1>
        <p class="mt-1 text-sm text-on-surface-variant">
            ¡Gracias por registrarte! Antes de comenzar, verifica tu correo electrónico con el enlace que te acabamos de enviar. Si no lo recibiste, con gusto te enviamos otro.
        </p>
    </div>

    @if (session('status') == 'verification-link-sent')
        <x-dash.alert type="success" class="mb-4">
            Se envió un nuevo enlace de verificación al correo que registraste.
        </x-dash.alert>
    @endif

    <div class="flex flex-col gap-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <x-primary-button class="w-full">
                Reenviar correo de verificación
            </x-primary-button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf

            <button type="submit" class="text-sm text-primary hover:underline">
                Cerrar sesión
            </button>
        </form>
    </div>
</x-guest-layout>
