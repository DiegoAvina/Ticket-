<x-app-layout title="Usuarios">
    <x-slot name="header">
        <x-dash.page-header title="Usuarios" icon="group" subtitle="Roles, áreas y acceso de las cuentas" />
    </x-slot>

    <div class="dash-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Área</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($usuarios as $usuario)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3 min-w-0">
                                    <x-dash.avatar :name="$usuario->name" class="w-8 h-8" />
                                    <div class="min-w-0">
                                        <p class="font-semibold text-on-surface truncate">{{ $usuario->name }}</p>
                                        <p class="text-xs text-on-surface-variant truncate">{{ $usuario->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="flex flex-wrap gap-1">
                                    @forelse ($usuario->roles as $rol)
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-primary-container/60 text-on-primary-container">{{ $rol->name }}</span>
                                    @empty
                                        <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-surface-container-highest text-on-surface-variant">sin rol</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="text-on-surface-variant">{{ $usuario->department?->name ?? '—' }}</td>
                            <td>
                                @if ($usuario->active)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-tertiary-container/50 text-on-tertiary-container dark:text-tertiary">Activo</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-surface-container-highest text-on-surface-variant">Desactivado</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.users.edit', $usuario) }}" class="btn-ghost btn-sm">
                                    <span class="material-symbols-outlined text-[16px]">edit</span> Editar
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center">
                                <span class="material-symbols-outlined text-[40px] text-outline">inbox</span>
                                <p class="mt-2 text-on-surface-variant">No hay usuarios registrados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($usuarios->hasPages())
            <div class="px-4 py-3 bg-surface-container">
                {{ $usuarios->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
