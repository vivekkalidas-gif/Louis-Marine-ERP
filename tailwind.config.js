import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    darkMode: 'class', // Enable class-based dark mode

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                marine: {
                    50: '#f4f6f8',
                    100: '#e3e8ee',
                    200: '#c5cedb',
                    300: '#9baabf',
                    400: '#6f85a0',
                    500: '#4d6582',
                    600: '#394e68',
                    700: '#2b3a4f',
                    800: '#1e293b', // Slate 800 - Sidebar Dark
                    900: '#0f172a', // Slate 900 - Deepest Navy
                    950: '#020617', // Nearly Black
                },
                accent: {
                    DEFAULT: '#2563eb', // Blue 600 - Professional accent
                    hover: '#1d4ed8',   // Blue 700
                },
            },
        },
    },

    plugins: [forms],
};
