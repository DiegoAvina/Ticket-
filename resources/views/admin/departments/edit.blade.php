<x-app-layout title="Editar departamento">
    <x-slot name="header">
        <x-dash.page-header title="Editar departamento" :subtitle="$departamento->name" :back="route('admin.departments.index')" />
    </x-slot>

    <div class="dash-card p-6 max-w-3xl w-full">
        <form method="POST" action="{{ route('admin.departments.update', $departamento) }}" class="flex flex-col gap-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="form-label">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $departamento->name) }}" class="form-control" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="description" class="form-label">Descripción (opcional)</label>
                <textarea id="description" name="description" rows="2" class="form-control">{{ old('description', $departamento->description) }}</textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div>
                <label class="inline-flex items-center gap-2 text-sm text-on-surface">
                    <input type="checkbox" name="active" value="1" class="form-check" @checked($departamento->active)>
                    Activo
                </label>
                <x-input-error :messages="$errors->get('active')" class="mt-2" />
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
