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
            'mr': "'Montserrat', sans-serif;",
            'rb': "'Roboto', sans-serif",
            'os': "'Open Sans', sans-serif",
        },
        fontSize: {
            '6': 'clamp(1.88rem, 0.72rem + 5.76vw, 6.25rem)',
            'hybrid1': 'clamp(4.38rem, -1.78rem + 30.77vw, 6.88rem)',
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
