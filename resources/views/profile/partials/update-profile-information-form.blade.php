<section>
    <header class="flex items-start gap-3">
        <span class="p-2 rounded-lg bg-primary-container/60 text-primary">
            <span class="material-symbols-outlined text-[20px]">badge</span>
        </span>
        <div>
            <h2 class="font-display text-lg font-bold text-on-surface">
                Información del perfil
            </h2>
            <p class="mt-0.5 text-sm text-on-surface-variant">
                Actualiza el nombre y el correo electrónico de tu cuenta.
            </p>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 flex flex-col gap-5">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nombre" />
            <x-text-input id="name" name="name" type="text" class="mt-1 w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="Correo electrónico" />
            <x-text-input id="email" name="email" type="email" class="mt-1 w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3">
                    <p class="text-sm text-on-surface">
                        Tu correo electrónico no está verificado.

                        <button form="send-verification" class="text-sm text-primary hover:underline">
                            Haz clic aquí para reenviar el correo de verificación.
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-tertiary">
                            Se envió un nuevo enlace de verificación a tu correo electrónico.
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                <span class="material-symbols-outlined text-[18px]">save</span>
                Guardar
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-tertiary"
                >Guardado.</p>
            @endif
        </div>
    </form>
</section>
