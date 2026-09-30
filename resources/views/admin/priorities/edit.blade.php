<x-app-layout title="Editar prioridad">
    <x-slot name="header">
        <x-dash.page-header title="Editar prioridad" :subtitle="$prioridad->name" :back="route('admin.priorities.index')" />
    </x-slot>

    <div class="dash-card p-6 max-w-3xl w-full">
        <form method="POST" action="{{ route('admin.priorities.update', $prioridad) }}" class="flex flex-col gap-5">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="form-label">Nombre</label>
                <input type="text" id="name" name="name" value="{{ old('name', $prioridad->name) }}" class="form-control" required>
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>
            <div>
                <label for="level" class="form-label">Nivel (mayor = más urgente)</label>
                <input type="number" id="level" name="level" value="{{ old('level', $prioridad->level) }}" min="1" class="form-control font-code" required>
                <x-input-error :messages="$errors->get('level')" class="mt-2" />
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label for="first_response_minutes" class="form-label">Minutos para primera respuesta</label>
                    <input type="number" id="first_response_minutes" name="first_response_minutes" value="{{ old('first_response_minutes', $prioridad->first_response_minutes) }}" min="1" class="form-control font-code" required>
                    <x-input-error :messages="$errors->get('first_response_minutes')" class="mt-2" />
                </div>
                <div>
                    <label for="resolution_minutes" class="form-label">Minutos para resolución</label>
                    <input type="number" id="resolution_minutes" name="resolution_minutes" value="{{ old('resolution_minutes', $prioridad->resolution_minutes) }}" min="1" class="form-control font-code" required>
                    <x-input-error :messages="$errors->get('resolution_minutes')" class="mt-2" />
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <a href="{{ route('admin.priorities.index') }}" class="btn-secondary">Cancelar</a>
                <button type="submit" class="btn-primary">
                    <span class="material-symbols-outlined text-[18px]">save</span> Guardar
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
