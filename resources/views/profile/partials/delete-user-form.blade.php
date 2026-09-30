<section class="flex flex-col gap-6">
    <header class="flex items-start gap-3">
        <span class="p-2 rounded-lg bg-error-container/60 text-error">
            <span class="material-symbols-outlined text-[20px]">person_remove</span>
        </span>
        <div>
            <h2 class="font-display text-lg font-bold text-error">
                Eliminar cuenta
            </h2>
            <p class="mt-0.5 text-sm text-on-surface-variant">
                Al eliminar tu cuenta, todos sus recursos y datos se borrarán de forma permanente. Antes de continuar, descarga cualquier dato o información que quieras conservar.
            </p>
        </div>
    </header>

    <div>
        <x-danger-button
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        >
            <span class="material-symbols-outlined text-[18px]">delete_forever</span>
            Eliminar cuenta
        </x-danger-button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <div class="flex items-start gap-3">
                <span class="p-2 rounded-lg bg-error-container/60 text-error">
                    <span class="material-symbols-outlined text-[22px]">warning</span>
                </span>
                <div>
                    <h2 class="font-display text-lg font-bold text-on-surface">
                        ¿Seguro que quieres eliminar tu cuenta?
                    </h2>
                    <p class="mt-1 text-sm text-on-surface-variant">
                        Al eliminar tu cuenta, todos sus recursos y datos se borrarán de forma permanente. Escribe tu contraseña para confirmar que deseas eliminarla definitivamente.
                    </p>
                </div>
            </div>

            <div class="mt-6">
                <x-input-label for="password" value="Contraseña" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 w-full sm:w-3/4"
                    placeholder="Contraseña"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    Cancelar
                </x-secondary-button>

                <x-danger-button>
                    <span class="material-symbols-outlined text-[18px]">delete_forever</span>
                    Eliminar cuenta
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
