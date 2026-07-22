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
                    lavender: '#b8b8d1',
                    indigo: '#5b5f97',
                    amber: '#ffc145',
                    cream: '#fffffb',
                    coral: '#ff6b6c',
                    'indigo-dark': '#4a4d7a',
                    'lavender-light': '#d4d4e8',
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
