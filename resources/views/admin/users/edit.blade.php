<x-app-layout title="Editar usuario">
    <x-slot name="header">
        <x-dash.page-header title="Editar usuario" :subtitle="$usuario->name" :back="route('admin.users.index')" />
    </x-slot>

    @if ($errors->any())
        <x-dash.alert type="error" title="Revisa los datos" class="max-w-3xl w-full">
            <ul class="list-disc ms-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-dash.alert>
    @endif

    <div class="dash-card p-6 max-w-3xl w-full">
        <div class="flex items-center gap-3 pb-5 mb-5 border-b border-outline-variant/40">
            <x-dash.avatar :name="$usuario->name" class="w-10 h-10" />
            <div class="min-w-0">
                <p class="font-semibold text-on-surface truncate">{{ $usuario->name }}</p>
                <p class="text-sm text-on-surface-variant truncate">{{ $usuario->email }}</p>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $usuario) }}" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <div>
                <span class="form-label">Roles (puede tener varios)</span>
                <div class="flex flex-col gap-2">
                    @foreach ($roles as $rol)
                        <label class="inline-flex items-center gap-2 text-sm text-on-surface">
                            <input type="checkbox" name="roles[]" value="{{ $rol->name }}" class="form-check" @checked($usuario->hasRole($rol->name))>
                            {{ ucfirst($rol->name) }}
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
            </div>

            <div>
                <label for="department_id" class="form-label">Área / Departamento</label>
                <select id="department_id" name="department_id" class="form-control">
                    <option value="">Sin área</option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}" @selected($usuario->department_id === $departamento->id)>
                            {{ $departamento->name }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>

            <div>
                <label class="inline-flex items-center gap-2 text-sm text-on-surface">
                    <input type="checkbox" name="active" value="1" class="form-check" @checked($usuario->active)>
                    Cuenta activa (puede iniciar sesión)
                </label>
                <x-input-error :messages="$errors->get('active')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
