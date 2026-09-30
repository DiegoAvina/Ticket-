<x-app-layout title="Prioridades">
    <x-slot name="header">
        <x-dash.page-header title="Prioridades" icon="flag" subtitle="Niveles de urgencia y tiempos de SLA">
            <a href="{{ route('admin.priorities.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span> Nueva prioridad
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
                        <th>Prioridad</th>
                        <th class="text-right">Nivel</th>
                        <th class="text-right">1a respuesta</th>
                        <th class="text-right">Resolución</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($prioridades as $prioridad)
                        <tr>
                            <td><x-dash.priority-badge :priority="$prioridad" /></td>
                            <td class="text-right font-code text-on-surface-variant">{{ $prioridad->level }}</td>
                            <td class="text-right font-code text-on-surface-variant whitespace-nowrap">{{ $prioridad->first_response_minutes }} min</td>
                            <td class="text-right font-code text-on-surface-variant whitespace-nowrap">{{ $prioridad->resolution_minutes }} min</td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.priorities.edit', $prioridad) }}" class="btn-ghost btn-sm">
                                    <span class="material-symbols-outlined text-[16px]">edit</span> Editar
                                </a>
                                <form method="POST" action="{{ route('admin.priorities.destroy', $prioridad) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar {{ $prioridad->name }}?');">
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
                                <p class="mt-2 text-on-surface-variant">No hay prioridades registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
