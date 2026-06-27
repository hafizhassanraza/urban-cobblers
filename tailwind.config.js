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
                sans: ['Cormorant Garamond', 'Georgia', ...defaultTheme.fontFamily.serif],
                display: ['Playfair Display', 'Georgia', ...defaultTheme.fontFamily.serif],
                body: ['Lato', ...defaultTheme.fontFamily.sans],
                nav: ['Plus Jakarta Sans', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    black: '#111111',
                    beige: '#D8C3A5',
                    white: '#FFFFFF',
                    copper: '#B87333',
                },
            },
        },
    },

    plugins: [forms],
};
