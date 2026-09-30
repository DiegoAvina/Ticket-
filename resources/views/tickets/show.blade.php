@php
    $codigoEstado = $ticket->status->code;
    $slaRestante = \App\Support\SlaReloj::restante($ticket);
    $slaProgreso = \App\Support\SlaReloj::progreso($ticket);
    $slaTono = \App\Support\SlaReloj::tono($ticket);
    $slaVariable = $slaTono === 'neutral' ? 'outline' : $slaTono;
@endphp
<x-app-layout :title="$ticket->ticket_number">
    <x-slot name="header">
        <x-dash.page-header :title="$ticket->title" :eyebrow="$ticket->ticket_number" :back="route('tickets.index')">
            <x-dash.status-badge :status="$ticket->status" />
            <x-dash.priority-badge :priority="$ticket->priority" />
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

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">

        {{-- Columna izquierda: descripción + conversación --}}
        <div class="xl:col-span-8 flex flex-col gap-6 min-w-0">

            {{-- Descripción --}}
            <x-dash.panel title="Descripción" icon="description" tone="primary">
                <div class="flex items-center gap-3">
                    <x-dash.avatar :name="$ticket->requester->name" class="w-9 h-9" />
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-on-surface truncate">{{ $ticket->requester->name }}</p>
                        <p class="font-code text-[11px] text-outline">{{ $ticket->created_at->locale('es')->diffForHumans() }}</p>
                    </div>
                </div>
                <p class="text-sm text-on-surface whitespace-pre-line leading-relaxed">{{ $ticket->description }}</p>
            </x-dash.panel>

            {{-- Conversación --}}
            <x-dash.panel title="Conversación" icon="forum" tone="tertiary">
                <x-slot:aside>
                    <span class="font-code text-xs text-outline">{{ $ticket->messages->count() }}</span>
                </x-slot:aside>

                <div class="flex flex-col gap-4">
                    @forelse ($ticket->messages as $mensaje)
                        @php $esInterna = $mensaje->type->value === 'internal'; @endphp
                        <div class="flex items-start gap-3">
                            <x-dash.avatar :name="$mensaje->user?->name" class="w-8 h-8 mt-0.5" />
                            <div @class([
                                'flex-1 min-w-0 rounded-xl rounded-tl-sm p-3',
                                'bg-secondary-container/30 ring-1 ring-inset ring-secondary/30' => $esInterna,
                                'bg-surface-container' => ! $esInterna,
                            ])>
                                <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1 mb-1">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-sm font-semibold text-on-surface truncate">{{ $mensaje->user?->name ?? 'Sistema' }}</span>
                                        @if ($esInterna)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-semibold bg-secondary-container/60 text-on-secondary-container dark:text-secondary">
                                                <span class="material-symbols-outlined text-[14px]">lock</span> Nota interna
                                            </span>
                                        @endif
                                    </div>
                                    <span class="font-code text-[11px] text-outline" title="{{ $mensaje->created_at->format('d/m/Y H:i') }}">
                                        {{ $mensaje->created_at->locale('es')->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-sm text-on-surface whitespace-pre-line">{{ $mensaje->body }}</p>

                                @if ($mensaje->attachments->isNotEmpty())
                                    <div class="mt-3 flex flex-wrap gap-2">
                                        @foreach ($mensaje->attachments as $adjunto)
                                            <a href="{{ route('tickets.attachments.download', [$ticket, $adjunto]) }}"
                                               class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-surface-container-high text-primary hover:bg-surface-bright transition-colors">
                                                <span class="material-symbols-outlined text-[16px]">attach_file</span>
                                                {{ $adjunto->original_name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center">
                            <span class="material-symbols-outlined text-[40px] text-outline">chat_bubble</span>
                            <p class="mt-2 text-sm text-on-surface-variant">Sin mensajes todavía.</p>
                        </div>
                    @endforelse
                </div>

                <form method="POST" action="{{ route('tickets.messages', $ticket) }}" enctype="multipart/form-data"
                      class="flex flex-col gap-3 pt-4 border-t border-outline-variant/40">
                    @csrf
                    <textarea name="body" rows="3" class="form-control" placeholder="Escribe un comentario..." required></textarea>
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex flex-wrap items-center gap-3">
                            <input type="file" name="adjuntos[]" multiple
                                   class="text-xs text-on-surface-variant file:mr-2 file:px-3 file:py-1.5 file:rounded-lg file:border-0 file:bg-surface-container-high file:text-on-surface file:text-xs file:font-semibold hover:file:bg-surface-bright">
                            @if ($puedeGestionar)
                                <label class="inline-flex items-center gap-2 text-xs text-on-surface-variant">
                                    <input type="checkbox" name="type" value="internal" class="form-check"> Nota interna
                                </label>
                            @endif
                        </div>
                        <button type="submit" class="btn-primary">
                            <span class="material-symbols-outlined text-[18px]">send</span> Comentar
                        </button>
                    </div>
                </form>
            </x-dash.panel>

            {{-- Historial --}}
            <x-dash.panel title="Historial" icon="history" tone="neutral">
                <ul class="divide-y divide-outline-variant/20 text-sm">
                    @foreach ($ticket->history as $evento)
                        <li class="flex items-center justify-between gap-3 py-2 first:pt-0 last:pb-0">
                            <span class="text-on-surface-variant min-w-0">
                                <span class="font-medium text-on-surface">{{ $evento->user?->name ?? 'Sistema' }}</span>
                                — {{ str_replace('_', ' ', $evento->action) }}
                            </span>
                            <span class="font-code text-[11px] text-outline whitespace-nowrap">{{ $evento->created_at->format('d/m/Y H:i') }}</span>
                        </li>
                    @endforeach
                </ul>
            </x-dash.panel>
        </div>

        {{-- Columna derecha: detalles, SLA y acciones --}}
        <div class="xl:col-span-4 flex flex-col gap-6 min-w-0">

            {{-- Detalles --}}
            <x-dash.panel title="Detalles" icon="info" tone="secondary">
                <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Estado</dt>
                        <dd class="mt-1 text-on-surface"><x-dash.status-badge :status="$ticket->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Prioridad</dt>
                        <dd class="mt-1 text-on-surface"><x-dash.priority-badge :priority="$ticket->priority" /></dd>
                    </div>
                    <div class="col-span-2">
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Área responsable</dt>
                        <dd class="text-on-surface">{{ $ticket->assignedDepartment->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Solicitante</dt>
                        <dd class="mt-1 flex items-center gap-2 text-on-surface min-w-0">
                            <x-dash.avatar :name="$ticket->requester->name" class="w-6 h-6 text-[10px]" />
                            <span class="truncate">{{ $ticket->requester->name }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Asignado a</dt>
                        <dd class="mt-1 flex items-center gap-2 text-on-surface min-w-0">
                            @if ($ticket->assignedUser)
                                <x-dash.avatar :name="$ticket->assignedUser->name" class="w-6 h-6 text-[10px]" />
                                <span class="truncate">{{ $ticket->assignedUser->name }}</span>
                            @else
                                <span class="text-outline">Sin asignar</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Servicio</dt>
                        <dd class="text-on-surface">{{ $ticket->service?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Categoría</dt>
                        <dd class="text-on-surface">{{ $ticket->category?->name ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Creado</dt>
                        <dd class="font-code text-xs text-on-surface">{{ $ticket->created_at->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Primera respuesta</dt>
                        <dd class="font-code text-xs text-on-surface">{{ $ticket->first_response_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Resuelto</dt>
                        <dd class="font-code text-xs text-on-surface">{{ $ticket->resolved_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-[11px] font-semibold uppercase tracking-wider text-outline">Cerrado</dt>
                        <dd class="font-code text-xs text-on-surface">{{ $ticket->closed_at?->format('d/m/Y H:i') ?? '—' }}</dd>
                    </div>
                </dl>
            </x-dash.panel>

            {{-- SLA de resolución --}}
            <x-dash.panel title="SLA de resolución" icon="timer" :tone="$slaTono">
                <x-slot:aside>
                    @if ($slaRestante)
                        <span class="font-code text-sm font-semibold {{ config("dashboard.tones.$slaTono.text") }}">{{ $slaRestante }}</span>
                    @endif
                </x-slot:aside>

                @if ($ticket->sla_resolution_due_at)
                    <div class="bar3d bar3d--thin">
                        <div class="bar3d__fill" style="--c: rgb(var(--{{ $slaVariable }})); width: {{ $slaProgreso }}%"></div>
                    </div>
                    <div class="flex items-center justify-between text-xs">
                        <span class="text-on-surface-variant">{{ $slaProgreso }}% del plazo consumido</span>
                        <span class="font-code text-outline">Vence {{ $ticket->sla_resolution_due_at->format('d/m/Y H:i') }}</span>
                    </div>
                @else
                    <p class="text-sm text-on-surface-variant">Este ticket no tiene un SLA de resolución definido.</p>
                @endif
            </x-dash.panel>

            @if ($codigoEstado === 'resolved')
            <x-dash.panel title="¿Quedó solucionado?" icon="task_alt" tone="tertiary">
                <p class="text-sm text-on-surface-variant">El ticket fue marcado como resuelto. Confirma si el problema quedó solucionado.</p>
                <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                    @csrf
                    <button type="submit" class="btn-primary w-full">
                        <span class="material-symbols-outlined text-[18px]">check_circle</span> Confirmar y cerrar
                    </button>
                </form>
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}" class="flex flex-col gap-2 pt-4 border-t border-outline-variant/40">
                    @csrf
                    <input type="text" name="motivo" placeholder="¿Por qué reabres?" class="form-control" required>
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined text-[18px]">replay</span> Reabrir
                    </button>
                </form>
            </x-dash.panel>
            @endif

            @if ($puedeGestionar)
            {{-- Panel de gestión --}}
            <x-dash.panel title="Gestión" icon="manage_accounts" tone="primary">
                <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="flex flex-col gap-2">
                    @csrf
                    <label class="form-label">Asignar a</label>
                    <div class="flex items-center gap-2">
                        <select name="agente_id" class="form-control" required>
                            <option value="">Seleccionar agente...</option>
                            @foreach ($agentesDelArea as $agente)
                                <option value="{{ $agente->id }}" @selected($ticket->assigned_user_id === $agente->id)>{{ $agente->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn-tonal shrink-0">Asignar</button>
                    </div>
                </form>

                @if (in_array($codigoEstado, ['assigned', 'waiting_user']))
                <form method="POST" action="{{ route('tickets.status', $ticket) }}">
                    @csrf
                    <input type="hidden" name="estado" value="in_progress">
                    <button type="submit" class="btn-primary w-full">
                        <span class="material-symbols-outlined text-[18px]">play_arrow</span> Iniciar atención
                    </button>
                </form>
                @endif

                @if ($codigoEstado === 'in_progress')
                <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="flex flex-col gap-2 pt-4 border-t border-outline-variant/40">
                    @csrf
                    <input type="hidden" name="estado" value="waiting_user">
                    <label class="form-label">Comentario (opcional)</label>
                    <input type="text" name="comentario" class="form-control">
                    <button type="submit" class="btn-secondary">
                        <span class="material-symbols-outlined text-[18px]">help</span> Pedir info al usuario
                    </button>
                </form>

                <form method="POST" action="{{ route('tickets.resolve', $ticket) }}" class="flex flex-col gap-2 pt-4 border-t border-outline-variant/40">
                    @csrf
                    <label class="form-label">Resolución</label>
                    <textarea name="resolucion" rows="2" class="form-control" required></textarea>
                    <button type="submit" class="btn-primary">
                        <span class="material-symbols-outlined text-[18px]">task_alt</span> Marcar como resuelto
                    </button>
                </form>
                @endif

                <details class="group text-sm pt-4 border-t border-outline-variant/40">
                    <summary class="cursor-pointer list-none flex items-center justify-between text-on-surface-variant hover:text-on-surface">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">trending_up</span> Escalar ticket
                        </span>
                        <span class="material-symbols-outlined text-[18px] transition-transform group-open:rotate-180">expand_more</span>
                    </summary>
                    <form method="POST" action="{{ route('tickets.escalate', $ticket) }}" class="mt-3 flex flex-col gap-2">
                        @csrf
                        <input type="text" name="motivo" placeholder="Motivo del escalamiento" class="form-control">
                        <button type="submit" class="btn-danger">Escalar</button>
                    </form>
                </details>
            </x-dash.panel>
            @endif

            @if ($esAdministrador)
            <x-dash.panel title="Solo administrador" icon="shield_person" tone="error" class="ring-1 ring-inset ring-error/20">
                <details class="group text-sm">
                    <summary class="cursor-pointer list-none flex items-center justify-between text-on-surface-variant hover:text-on-surface">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">edit</span> Editar datos del ticket
                        </span>
                        <span class="material-symbols-outlined text-[18px] transition-transform group-open:rotate-180">expand_more</span>
                    </summary>
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}" class="mt-3 flex flex-col gap-3">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label class="form-label mb-1">Título</label>
                            <input type="text" name="title" value="{{ old('title', $ticket->title) }}" class="form-control" required maxlength="255">
                        </div>
                        <div>
                            <label class="form-label mb-1">Descripción</label>
                            <textarea name="description" rows="3" class="form-control" required>{{ old('description', $ticket->description) }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="form-label mb-1">Prioridad</label>
                                <select name="priority_id" class="form-control" required>
                                    @foreach ($prioridades as $prioridad)
                                        <option value="{{ $prioridad->id }}" @selected(old('priority_id', $ticket->priority_id) == $prioridad->id)>{{ $prioridad->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="form-label mb-1">Categoría</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Sin categoría</option>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" @selected(old('category_id', $ticket->category_id) == $categoria->id)>{{ $categoria->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <button type="submit" class="btn-primary">Guardar cambios</button>
                    </form>
                </details>

                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="pt-4 border-t border-outline-variant/40"
                      onsubmit="return confirm('¿Eliminar definitivamente el ticket {{ $ticket->ticket_number }}? Esta acción no se puede deshacer desde la interfaz.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn-danger w-full">
                        <span class="material-symbols-outlined text-[18px]">delete</span> Eliminar ticket
                    </button>
                </form>
            </x-dash.panel>
            @endif
        </div>
    </div>
</x-app-layout>
