module.exports = {
    purge: [
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: false, // or 'media' or 'class'
    theme: {
        fontFamily: {
            'rb': "'Roboto', sans-serif",
            'os': "'Open Sans', sans-serif",
        },
        fontSize: {
            '480': 'clamp(1.31rem, -6.88rem + 40.98vw, 30.00rem)',
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
