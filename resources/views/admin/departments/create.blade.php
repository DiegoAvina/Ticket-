<x-app-layout title="Nuevo departamento">
    <x-slot name="header">
        <x-dash.page-header title="Nuevo departamento" subtitle="Registra un área que atiende solicitudes" :back="route('admin.departments.index')" />
    </x-slot>

    <div class="dash-card p-6 max-w-3xl w-full">
        <form method="POST" action="{{ route('admin.departments.store') }}" class="flex flex-col gap-5">
            @csrf
            <div>
                <label for="name" class="form-label">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="description" class="form-label">Descripción (opcional)</label>
                <textarea id="description" name="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.departments.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
