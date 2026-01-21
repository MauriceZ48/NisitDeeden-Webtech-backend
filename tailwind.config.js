import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: "#0066FF",
                // secondary: "#64748b",
                // success: "#16a34a",
                // danger: "#dc2626",
                approved: "#6EB04F",
                rejected: "#D42B2B",
                pending: "#FFBF00",
                background: "#f8fafc",
            },
        },
    },

    plugins: [forms],
};
