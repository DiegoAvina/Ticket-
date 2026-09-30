<x-app-layout title="Nuevo servicio">
    <x-slot name="header">
        <x-dash.page-header title="Nuevo servicio" subtitle="Agrega un servicio al catálogo de solicitudes" :back="route('admin.services.index')" />
    </x-slot>

    <div class="dash-card p-6 max-w-3xl w-full">
        <form method="POST" action="{{ route('admin.services.store') }}" class="flex flex-col gap-5">
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

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="category_id" class="form-label">Categoría (opcional)</label>
                    <select id="category_id" name="category_id" class="form-control">
                        <option value="">Sin categoría</option>
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}" @selected(old('category_id') == $categoria->id)>{{ $categoria->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <label for="default_priority_id" class="form-label">Prioridad por defecto (opcional)</label>
                    <select id="default_priority_id" name="default_priority_id" class="form-control">
                        <option value="">Sin prioridad por defecto</option>
                        @foreach ($prioridades as $prioridad)
                            <option value="{{ $prioridad->id }}" @selected(old('default_priority_id') == $prioridad->id)>{{ $prioridad->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('default_priority_id')" class="mt-2" />
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.services.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
