<x-app-layout title="Departamentos">
    <x-slot name="header">
        <x-dash.page-header title="Departamentos" icon="corporate_fare" subtitle="Áreas que atienden solicitudes">
            <a href="{{ route('admin.departments.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span> Nuevo departamento
            </a>
        </x-dash.page-header>
    </x-slot>

    @if ($errors->any())
        <x-dash.alert type="error" title="Revisa los datos">
            <ul class="list-disc ms-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-dash.alert>
    @endif

    <div class="dash-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th class="text-right">Usuarios</th>
                        <th class="text-right">Tickets</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($departamentos as $departamento)
                        <tr>
                            <td class="font-semibold text-on-surface">{{ $departamento->name }}</td>
                            <td class="text-right font-code text-on-surface-variant">{{ $departamento->users_count }}</td>
                            <td class="text-right font-code text-on-surface-variant">{{ $departamento->tickets_count }}</td>
                            <td>
                                @if ($departamento->active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-tertiary-container/50 text-on-tertiary-container dark:text-tertiary">Activo</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-surface-container-highest text-on-surface-variant">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.departments.edit', $departamento) }}" class="btn-ghost btn-sm">
                                    <span class="material-symbols-outlined text-[16px]">edit</span> Editar
                                </a>
                                <form method="POST" action="{{ route('admin.departments.destroy', $departamento) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar {{ $departamento->name }}? Esto también elimina sus servicios y categorías.');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-error">
                                        <span class="material-symbols-outlined text-[16px]">delete</span> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <span class="material-symbols-outlined text-[40px] text-outline">inbox</span>
                                <p class="mt-2 text-on-surface-variant">No hay departamentos registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
