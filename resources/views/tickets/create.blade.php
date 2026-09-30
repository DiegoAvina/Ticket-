<x-app-layout title="Nueva solicitud">
    <x-slot name="header">
        <x-dash.page-header title="¿Qué necesitas?" subtitle="Cuéntanos tu problema o solicitud y la canalizaremos al área correcta." :back="route('tickets.index')" />
    </x-slot>

    <div class="max-w-3xl w-full flex flex-col gap-6">

        @if ($errors->any())
            <x-dash.alert type="error" title="Revisa los datos">
                <ul class="list-disc ms-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-dash.alert>
        @endif

        <div class="dash-card p-6">
            <form method="POST" action="{{ route('tickets.store') }}" class="flex flex-col gap-5">
                @csrf

                <div>
                    <label for="service_id" class="form-label mb-1">Servicio</label>
                    <select name="service_id" id="service_id" class="form-control" required>
                        <option value="">Selecciona un servicio...</option>
                        @foreach ($serviciosPorDepartamento as $departamento => $servicios)
                            <optgroup label="{{ $departamento }}">
                                @foreach ($servicios as $servicio)
                                    <option value="{{ $servicio->id }}" @selected(old('service_id') == $servicio->id)>
                                        {{ $servicio->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div id="sugerencias-articulos" class="hidden rounded-xl p-4 bg-primary-container/40 ring-1 ring-inset ring-primary/30">
                    <p class="mb-2 flex items-center gap-1.5 text-[11px] font-semibold uppercase tracking-wider text-primary">
                        <span class="material-symbols-outlined text-[18px]">lightbulb</span>
                        ¿Esto te ayuda?
                    </p>
                    <ul id="sugerencias-articulos-lista" class="space-y-1 text-sm text-on-primary-container dark:text-on-surface"></ul>
                </div>

                @if ($misActivos->isNotEmpty())
                <div>
                    <label for="asset_id" class="form-label mb-1">¿Sobre qué equipo? (opcional)</label>
                    <select name="asset_id" id="asset_id" class="form-control">
                        <option value="">No aplica / no es sobre un equipo específico</option>
                        @foreach ($misActivos as $activo)
                            <option value="{{ $activo->id }}" @selected(old('asset_id') == $activo->id)>
                                {{ $activo->type }} — {{ $activo->name }}{{ $activo->serial_number ? " ({$activo->serial_number})" : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div>
                    <label for="title" class="form-label mb-1">Título</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" class="form-control" required maxlength="255">
                </div>

                <div>
                    <label for="description" class="form-label mb-1">Describe el problema o lo que necesitas</label>
                    <textarea name="description" id="description" rows="5" class="form-control" required>{{ old('description') }}</textarea>
                    <p class="form-hint">Incluye qué pasó, desde cuándo y cualquier mensaje de error que veas.</p>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-outline-variant/40">
                    <a href="{{ route('tickets.index') }}" class="btn-ghost">Cancelar</a>
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined text-[18px]">send</span> Enviar solicitud
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        const articulosPorServicio = @json($articulosPorServicio);

        document.getElementById('service_id')?.addEventListener('change', function (evento) {
            const contenedor = document.getElementById('sugerencias-articulos');
            const lista = document.getElementById('sugerencias-articulos-lista');
            const articulos = articulosPorServicio[evento.target.value] ?? [];

            lista.innerHTML = '';

            if (articulos.length === 0) {
                contenedor.classList.add('hidden');
                return;
            }

            articulos.forEach((articulo) => {
                const item = document.createElement('li');
                item.textContent = '📄 ' + articulo.title;
                lista.appendChild(item);
            });

            contenedor.classList.remove('hidden');
        });
    </script>
</x-app-layout>
