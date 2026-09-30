<x-app-layout title="Servicios">
    <x-slot name="header">
        <x-dash.page-header title="Servicios" icon="design_services" subtitle="Catálogo de servicios que se pueden solicitar">
            <a href="{{ route('admin.services.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span> Nuevo servicio
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
                        <th>Servicio</th>
                        <th>Área</th>
                        <th>Categoría</th>
                        <th>Prioridad por defecto</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($servicios as $servicio)
                        <tr>
                            <td class="font-semibold text-on-surface">{{ $servicio->name }}</td>
                            <td class="text-on-surface-variant">{{ $servicio->department->name }}</td>
                            <td class="text-on-surface-variant">{{ $servicio->category?->name ?? '—' }}</td>
                            <td>
                                @if ($servicio->defaultPriority)
                                    <x-dash.priority-badge :priority="$servicio->defaultPriority" />
                                @else
                                    <span class="text-outline">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($servicio->active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-tertiary-container/50 text-on-tertiary-container dark:text-tertiary">Activo</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-surface-container-highest text-on-surface-variant">Inactivo</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.services.edit', $servicio) }}" class="btn-ghost btn-sm">
                                    <span class="material-symbols-outlined text-[16px]">edit</span> Editar
                                </a>
                                <form method="POST" action="{{ route('admin.services.destroy', $servicio) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar {{ $servicio->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-error">
                                        <span class="material-symbols-outlined text-[16px]">delete</span> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <span class="material-symbols-outlined text-[40px] text-outline">inbox</span>
                                <p class="mt-2 text-on-surface-variant">No hay servicios registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
