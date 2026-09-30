<x-app-layout title="Categorías">
    <x-slot name="header">
        <x-dash.page-header title="Categorías" icon="category" subtitle="Administración del catálogo de categorías por área">
            <a href="{{ route('admin.categories.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add</span> Nueva categoría
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
                        <th>Categoría</th>
                        <th>Área</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categorias as $categoria)
                        <tr>
                            <td class="font-semibold text-on-surface">{{ $categoria->name }}</td>
                            <td class="text-on-surface-variant">{{ $categoria->department->name }}</td>
                            <td class="text-right whitespace-nowrap">
                                <a href="{{ route('admin.categories.edit', $categoria) }}" class="btn-ghost btn-sm">
                                    <span class="material-symbols-outlined text-[16px]">edit</span> Editar
                                </a>
                                <form method="POST" action="{{ route('admin.categories.destroy', $categoria) }}" class="inline"
                                      onsubmit="return confirm('¿Eliminar {{ $categoria->name }}?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-ghost btn-sm text-error">
                                        <span class="material-symbols-outlined text-[16px]">delete</span> Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="py-12 text-center">
                                <span class="material-symbols-outlined text-[40px] text-outline">inbox</span>
                                <p class="mt-2 text-on-surface-variant">No hay categorías registradas.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
