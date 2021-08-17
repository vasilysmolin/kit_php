const defaultTheme = require('tailwindcss/defaultTheme')
module.exports = {
    mode: 'jit',
    purge: [
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        screens: {
            'xs': '450px',
            ...defaultTheme.screens,
        },
        fontFamily: {
            'rb': "'Roboto', sans-serif",
            'os': "'Open Sans', sans-serif",
        },
        fontSize: {
            '6': 'clamp(1.88rem, 0.72rem + 5.76vw, 6.25rem)',
            '28': 'clamp(5.63rem, -0.29rem + 29.56vw, 28.13rem)',
        },
        extend: {
            container: {
                center: true,
            }
        },
    },
    variants: {
        extend: {},
    },
    plugins: [],
    }
