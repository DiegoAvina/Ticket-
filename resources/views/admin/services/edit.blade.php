<x-app-layout title="Editar servicio">
    <x-slot name="header">
        <x-dash.page-header title="Editar servicio" :subtitle="$servicio->name" :back="route('admin.services.index')" />
    </x-slot>

    <div class="dash-card p-6 max-w-3xl w-full">
        <form method="POST" action="{{ route('admin.services.update', $servicio) }}" class="flex flex-col gap-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="form-label">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $servicio->name) }}" class="form-control" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <div>
                <label for="department_id" class="form-label">Área</label>
                <select id="department_id" name="department_id" class="form-control" required>
                    @foreach ($departamentos as $departamento)
                        <option value="{{ $departamento->id }}" @selected(old('department_id', $servicio->department_id) == $departamento->id)>{{ $departamento->name }}</option>
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
                            <option value="{{ $categoria->id }}" @selected(old('category_id', $servicio->category_id) == $categoria->id)>{{ $categoria->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
                </div>

                <div>
                    <label for="default_priority_id" class="form-label">Prioridad por defecto (opcional)</label>
                    <select id="default_priority_id" name="default_priority_id" class="form-control">
                        <option value="">Sin prioridad por defecto</option>
                        @foreach ($prioridades as $prioridad)
                            <option value="{{ $prioridad->id }}" @selected(old('default_priority_id', $servicio->default_priority_id) == $prioridad->id)>{{ $prioridad->name }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('default_priority_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <label class="inline-flex items-center gap-2 text-sm text-on-surface">
                    <input type="checkbox" name="active" value="1" class="form-check" @checked($servicio->active)>
                    Activo
                </label>
                <x-input-error :messages="$errors->get('active')" class="mt-2" />
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
