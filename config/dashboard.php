<?php

/*
 * Paleta del dashboard. Este archivo está en el `content` de Tailwind, así que
 * las clases de `tones` se generan aunque solo aparezcan aquí.
 */
return [
    // Colores (hex) de las barras, columnas 3D y la dona, por código de estado.
    'status_colors' => [
        'new' => '#8b5cf6',
        'assigned' => '#64748b',
        'in_progress' => '#14b8a6',
        'waiting_user' => '#d946ef',
        'resolved' => '#10b981',
        'closed' => '#94a3b8',
        'cancelled' => '#f43f5e',
    ],

    // Colores rotativos para series sin semántica propia (áreas, agentes).
    'series_colors' => ['#d946ef', '#14b8a6', '#8b5cf6', '#f59e0b', '#0ea5e9', '#ec4899', '#10b981', '#f97316'],

    // Tono semántico por código de estado y por nivel de prioridad.
    'status_tones' => [
        'new' => 'secondary',
        'assigned' => 'neutral',
        'in_progress' => 'tertiary',
        'waiting_user' => 'primary',
        'resolved' => 'tertiary',
        'closed' => 'neutral',
        'cancelled' => 'error',
    ],
    'priority_tones' => [1 => 'neutral', 2 => 'tertiary', 3 => 'secondary', 4 => 'error'],

    // Clases por tono: chip de ícono, texto, píldora suave y barra/punto sólido.
    'tones' => [
        'primary' => ['chip' => 'bg-primary-container/60 text-primary', 'text' => 'text-primary', 'pill' => 'bg-primary-container/50 text-on-primary-container dark:text-primary', 'solid' => 'bg-primary'],
        'secondary' => ['chip' => 'bg-secondary-container/60 text-secondary', 'text' => 'text-secondary', 'pill' => 'bg-secondary-container/50 text-on-secondary-container dark:text-secondary', 'solid' => 'bg-secondary'],
        'tertiary' => ['chip' => 'bg-tertiary-container/60 text-tertiary', 'text' => 'text-tertiary', 'pill' => 'bg-tertiary-container/50 text-on-tertiary-container dark:text-tertiary', 'solid' => 'bg-tertiary'],
        'error' => ['chip' => 'bg-error-container/60 text-error', 'text' => 'text-error', 'pill' => 'bg-error-container/60 text-on-error-container dark:text-error', 'solid' => 'bg-error'],
        'neutral' => ['chip' => 'bg-surface-container-highest text-on-surface-variant', 'text' => 'text-outline', 'pill' => 'bg-surface-container-highest text-on-surface-variant', 'solid' => 'bg-outline'],
    ],
];
