import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

// Tokens de color del dashboard: se resuelven con variables CSS definidas en
// resources/css/app.css (:root = claro, .dark = oscuro).
const token = (name) => `rgb(var(--${name}) / <alpha-value>)`;
const tokens = [
    'surface', 'surface-bright', 'surface-container-lowest', 'surface-container-low',
    'surface-container', 'surface-container-high', 'surface-container-highest',
    'on-surface', 'on-surface-variant', 'outline', 'outline-variant',
    'primary', 'on-primary', 'primary-container', 'on-primary-container',
    'secondary', 'secondary-container', 'on-secondary-container',
    'tertiary', 'tertiary-container', 'on-tertiary-container',
    'error', 'error-container', 'on-error-container',
];

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './config/dashboard.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
                display: ['"Plus Jakarta Sans"', ...defaultTheme.fontFamily.sans],
                code: ['"JetBrains Mono"', ...defaultTheme.fontFamily.mono],
            },
            colors: Object.fromEntries(tokens.map((name) => [name, token(name)])),
        },
    },

    plugins: [forms],
};
