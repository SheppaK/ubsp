import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class',

    theme: {
        extend: {
            colors: {
                brand: {
                    lavender: 'rgb(var(--color-lavender) / <alpha-value>)',
                    indigo: 'rgb(var(--color-indigo) / <alpha-value>)',
                    amber: 'rgb(var(--color-amber) / <alpha-value>)',
                    cream: 'rgb(var(--color-cream) / <alpha-value>)',
                    coral: 'rgb(var(--color-coral) / <alpha-value>)',
                    'indigo-dark': 'rgb(var(--color-indigo-dark) / <alpha-value>)',
                    'lavender-light': 'rgb(var(--color-lavender) / <alpha-value>)',
                    'page-dark': 'rgb(var(--color-page-dark) / <alpha-value>)',
                    'surface-dark': 'rgb(var(--color-surface-dark) / <alpha-value>)',
                },
            },
            fontFamily: {
                sans: ['Poppins', ...defaultTheme.fontFamily.sans],
                heading: ['Raleway', ...defaultTheme.fontFamily.sans],
            },
            borderRadius: {
                '4xl': '2rem',
            },
            boxShadow: {
                brand: '0 8px 32px rgba(91, 95, 151, 0.12)',
                'brand-lg': '0 16px 48px rgba(91, 95, 151, 0.18)',
                coral: '0 8px 24px rgba(255, 107, 108, 0.25)',
            },
        },
    },

    plugins: [forms],
};
