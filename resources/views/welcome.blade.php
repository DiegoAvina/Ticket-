<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name', 'Help-Dasa') }} · Mesa de ayuda Dasavena</title>
        <meta name="description" content="Mesa de ayuda interna de Dasavena: levanta solicitudes, da seguimiento y recibe solución a tiempo.">
        @include('layouts.partials.head')
    </head>
    <body class="font-inter text-sm antialiased bg-surface text-on-surface transition-colors duration-300">
        @php
            $oro = '#e8c063';
            $pasos = [
                ['icon' => 'edit_note', 'titulo' => 'Crea tu solicitud', 'texto' => 'Elige el servicio que necesitas y cuéntanos qué pasa. Te sugerimos artículos que podrían resolverlo al instante.'],
                ['icon' => 'route', 'titulo' => 'Llega al área correcta', 'texto' => 'Tu ticket se asigna al departamento responsable y un agente lo toma para atenderlo.'],
                ['icon' => 'notifications_active', 'titulo' => 'Sigue cada avance', 'texto' => 'Recibe avisos por correo, conversa con el agente y consulta el estado en todo momento.'],
                ['icon' => 'task_alt', 'titulo' => 'Solución a tiempo', 'texto' => 'Cada prioridad tiene un tiempo de respuesta y resolución (SLA) que el equipo cuida de cerca.'],
            ];
            $beneficios = [
                ['icon' => 'timer', 'tone' => 'tertiary', 'titulo' => 'Tiempos claros', 'texto' => 'Ves cuánto falta para que tu solicitud sea atendida.'],
                ['icon' => 'forum', 'tone' => 'primary', 'titulo' => 'Todo en un lugar', 'texto' => 'Mensajes, adjuntos e historial dentro de cada ticket.'],
                ['icon' => 'lock', 'tone' => 'secondary', 'titulo' => 'Acceso con tu cuenta', 'texto' => 'Entra con tu cuenta Microsoft de la empresa.'],
            ];
        @endphp

        {{-- Navegación --}}
        <header class="sticky top-0 z-30 bg-surface/80 backdrop-blur-xl border-b border-outline-variant/20">
            <div class="max-w-6xl mx-auto h-16 px-4 sm:px-6 flex items-center justify-between gap-4">
                <a href="/" class="flex items-center gap-2">
                    <x-application-logo class="w-10 h-10" />
                    <span class="font-display text-lg font-bold tracking-tight">{{ config('app.name', 'Help-Dasa') }}</span>
                </a>
                <nav class="flex items-center gap-2">
                    <x-dash.theme-toggle />
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn-primary">
                                <span class="material-symbols-outlined text-[18px]">space_dashboard</span> Ir a mi panel
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn-primary">
                                <span class="material-symbols-outlined text-[18px]">login</span> Iniciar sesión
                            </a>
                        @endauth
                    @endif
                </nav>
            </div>
        </header>

        <main>
            {{-- Hero --}}
            <section class="relative overflow-hidden">
                <div class="absolute -right-40 -top-40 w-[34rem] h-[34rem] rounded-full bg-gradient-to-br from-primary-container/70 to-tertiary/10 blur-3xl pointer-events-none"></div>
                <div class="absolute -left-40 bottom-0 w-[28rem] h-[28rem] rounded-full blur-3xl pointer-events-none opacity-30" style="background: {{ $oro }}"></div>

                <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-16 lg:py-24 grid lg:grid-cols-2 gap-12 items-center">
                    <div class="flex flex-col gap-5">
                        <span class="inline-flex items-center gap-2 w-fit px-3 py-1 rounded-full bg-surface-container text-[11px] font-semibold uppercase tracking-wider text-on-surface-variant ring-1 ring-outline-variant/30">
                            <span class="w-2 h-2 rounded-full" style="background: {{ $oro }}"></span>
                            Mesa de ayuda · Dasavena
                        </span>
                        <h1 class="font-display text-4xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                            Te ayudamos como en
                            <span class="bg-gradient-to-r from-primary to-secondary bg-clip-text text-transparent">familia</span>.
                        </h1>
                        <p class="text-base text-on-surface-variant max-w-xl">
                            {{ config('app.name', 'Help-Dasa') }} es el canal para pedir soporte a las áreas de Dasavena.
                            Levanta una solicitud, da seguimiento a su avance y recibe una solución dentro del tiempo comprometido.
                        </p>

                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            @auth
                                <a href="{{ route('tickets.create') }}" class="btn-primary px-5 py-2.5 text-base">
                                    <span class="material-symbols-outlined text-[20px]">add_box</span> Nueva solicitud
                                </a>
                                <a href="{{ route('tickets.index') }}" class="btn-secondary px-5 py-2.5 text-base">Ver mis tickets</a>
                            @else
                                @if (Route::has('auth.microsoft.redirect'))
                                    <a href="{{ route('auth.microsoft.redirect') }}" class="btn-primary px-5 py-2.5 text-base">
                                        <svg width="18" height="18" viewBox="0 0 21 21" aria-hidden="true"><rect x="1" y="1" width="9" height="9" fill="#f25022"/><rect x="11" y="1" width="9" height="9" fill="#7fba00"/><rect x="1" y="11" width="9" height="9" fill="#00a4ef"/><rect x="11" y="11" width="9" height="9" fill="#ffb900"/></svg>
                                        Entrar con Microsoft
                                    </a>
                                @endif
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="btn-secondary px-5 py-2.5 text-base">Entrar con correo</a>
                                @endif
                            @endauth
                        </div>
                    </div>

                    {{-- Ilustración: ticket de ejemplo --}}
                    <div class="relative flex justify-center" aria-hidden="true">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <x-application-logo class="w-80 h-80 opacity-10 blur-[1px]" />
                        </div>

                        <div class="relative w-full max-w-md flex flex-col gap-4">
                            <div class="dash-card p-5 rotate-1 hover:rotate-0 transition-transform duration-500">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="font-code text-xs font-semibold px-1.5 rounded bg-tertiary-container/50 text-on-tertiary-container dark:text-tertiary">TCK-0001</span>
                                    <span class="inline-flex items-center gap-1.5 text-xs text-tertiary">
                                        <span class="pulse-dot w-1.5 h-1.5" style="--c: currentColor"></span> En proceso
                                    </span>
                                </div>
                                <p class="mt-3 font-display font-semibold text-base">La impresora de la oficina no imprime</p>
                                <p class="mt-1 text-xs text-outline">Soporte de TI · Impresoras</p>
                                <div class="mt-4 flex items-center justify-between text-xs">
                                    <span class="text-on-surface-variant">SLA de resolución</span>
                                    <span class="font-code font-semibold text-tertiary">3h 20m</span>
                                </div>
                                <div class="mt-1.5 bar3d">
                                    <div class="bar3d__fill" style="--c: rgb(var(--tertiary)); width: 38%"></div>
                                </div>
                            </div>

                            <div class="dash-card p-4 -rotate-1 ml-10 hover:rotate-0 transition-transform duration-500">
                                <div class="flex items-start gap-3">
                                    <span class="w-8 h-8 shrink-0 rounded-full bg-primary-container text-on-primary-container flex items-center justify-center">
                                        <span class="material-symbols-outlined text-[18px]">support_agent</span>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-xs font-semibold">Agente de soporte</p>
                                        <p class="mt-1 text-xs text-on-surface-variant rounded-lg bg-surface-container px-3 py-2">¡Hola! Ya estoy revisando tu equipo, te aviso en cuanto quede listo.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="dash-card p-3 w-fit ml-auto flex items-center gap-2">
                                <span class="material-symbols-outlined icon-fill text-[20px] text-tertiary">check_circle</span>
                                <span class="text-xs font-medium">Te notificaremos por correo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Cómo funciona --}}
            <section class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
                <div class="text-center max-w-2xl mx-auto">
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-tertiary">Cómo funciona</p>
                    <h2 class="mt-1 font-display text-3xl font-bold tracking-tight">De tu solicitud a la solución</h2>
                    <p class="mt-2 text-on-surface-variant">Un proceso sencillo, con seguimiento en cada paso.</p>
                </div>

                <ol class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    @foreach ($pasos as $i => $paso)
                        <li class="dash-card p-5 flex flex-col gap-3 hover:-translate-y-1 transition-transform duration-300">
                            <div class="flex items-center justify-between">
                                <span class="p-2 rounded-lg bg-primary-container/60 text-primary">
                                    <span class="material-symbols-outlined text-[22px]">{{ $paso['icon'] }}</span>
                                </span>
                                <span class="font-code text-2xl font-bold text-outline-variant">0{{ $i + 1 }}</span>
                            </div>
                            <h3 class="font-display font-semibold text-base">{{ $paso['titulo'] }}</h3>
                            <p class="text-on-surface-variant">{{ $paso['texto'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>

            {{-- Beneficios + llamado --}}
            <section class="max-w-6xl mx-auto px-4 sm:px-6 pb-20">
                <div class="relative overflow-hidden rounded-2xl bg-surface-container p-8 sm:p-10 shadow-xl">
                    <div class="absolute -right-24 -bottom-24 w-80 h-80 rounded-full blur-3xl opacity-30 pointer-events-none" style="background: {{ $oro }}"></div>

                    <div class="relative grid lg:grid-cols-5 gap-8 items-center">
                        <div class="lg:col-span-2 flex flex-col gap-3">
                            <h2 class="font-display text-2xl sm:text-3xl font-bold tracking-tight">¿Algo no funciona? Cuéntanos.</h2>
                            <p class="text-on-surface-variant">Entra con tu cuenta de la empresa y levanta tu primera solicitud en menos de un minuto.</p>
                            <div class="pt-2">
                                @auth
                                    <a href="{{ route('tickets.create') }}" class="btn-primary">
                                        <span class="material-symbols-outlined text-[18px]">add_box</span> Crear solicitud
                                    </a>
                                @else
                                    @if (Route::has('login'))
                                        <a href="{{ route('login') }}" class="btn-primary">
                                            <span class="material-symbols-outlined text-[18px]">login</span> Iniciar sesión
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>

                        <ul class="lg:col-span-3 grid sm:grid-cols-3 gap-3">
                            @foreach ($beneficios as $b)
                                <li class="rounded-xl bg-surface-container-low p-4 flex flex-col gap-2 ring-1 ring-outline-variant/20">
                                    <span class="w-fit p-1.5 rounded-lg {{ config("dashboard.tones.{$b['tone']}.chip") }}">
                                        <span class="material-symbols-outlined text-[20px]">{{ $b['icon'] }}</span>
                                    </span>
                                    <p class="font-semibold">{{ $b['titulo'] }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $b['texto'] }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-outline-variant/20">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-outline">
                <div class="flex items-center gap-2">
                    <x-application-logo class="w-6 h-6" />
                    <span>© {{ now()->year }} Dasavena · Recetas de familia</span>
                </div>
                <span>{{ config('app.name', 'Help-Dasa') }} — Mesa de ayuda interna</span>
            </div>
        </footer>
    </body>
</html>
