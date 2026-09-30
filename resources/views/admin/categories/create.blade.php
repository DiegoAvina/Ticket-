<x-app-layout title="Nueva categoría">
    <x-slot name="header">
        <x-dash.page-header title="Nueva categoría" subtitle="Registra una categoría dentro de un área" :back="route('admin.categories.index')" />
    </x-slot>

    <div class="dash-card p-6 max-w-3xl w-full">
        <form method="POST" action="{{ route('admin.categories.store') }}" class="flex flex-col gap-5">
            @csrf
            <div>
                <label for="name" class="form-label">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="department_id" class="form-label">Área</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    <option value="">Selecciona un área...</option>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}" @selected(old('department_id') == $departamento->id)>{{ $departamento->name }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.categories.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
