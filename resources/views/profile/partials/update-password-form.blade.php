<section>
    <header class="flex items-start gap-3">
        <span class="p-2 rounded-lg bg-secondary-container/60 text-secondary">
            <span class="material-symbols-outlined text-[20px]">key</span>
        </span>
        <div>
            <h2 class="font-display text-lg font-bold text-on-surface">
                Actualizar contraseña
            </h2>
            <p class="mt-0.5 text-sm text-on-surface-variant">
                Usa una contraseña larga y aleatoria para mantener tu cuenta segura.
            </p>
        </div>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 flex flex-col gap-5">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Contraseña actual" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Nueva contraseña" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Confirmar contraseña" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>
                <span class="material-symbols-outlined text-[18px]">save</span>
                Guardar
            </x-primary-button>

            @if (session('status') === 'password-updated')
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
