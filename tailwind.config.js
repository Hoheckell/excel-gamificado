import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Montserrat', ...defaultTheme.fontFamily.sans],
                montserrat: ['Montserrat', ...defaultTheme.fontFamily.sans],
                playfair: ['"Playfair Display"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                excel: {
                    dark: '#107c41',
                    light: '#1f9a55',
                    tint: '#e1f3e9',
                    grid: '#d0ded7',
                },
            },
            borderRadius: {
                excel: '6px',
            },
            boxShadow: {
                'excel': '0 8px 20px rgba(16, 124, 65, 0.08)',
                'excel-lg': '0 15px 40px rgba(16, 124, 65, 0.12)',
            },
        },
    },

    plugins: [forms, typography],
};
