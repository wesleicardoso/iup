import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            // --- DEFINIÇÃO DAS CORES SESI ---
            colors: {
                'sesi-blue': {
                    DEFAULT: '#003399', // Azul principal (Header/Sidebar)
                    dark: '#002266',    // Azul mais escuro (Fundo da página)
                    light: '#3366cc',   // Azul mais claro para hovers
                },
                'sesi-green': {
                    DEFAULT: '#7AC142', // Verde principal (Botões/Destaques)
                    light: '#9bd46c',   // Verde mais claro para hovers
                }
            }
            // --------------------------------
        },
    },

    plugins: [forms],
};