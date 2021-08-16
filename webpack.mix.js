const mix = require('laravel-mix');
const imagemin = require('imagemin');
const imageminWebp = require('imagemin-webp');
const rimraf = require('rimraf');

(async () => {
    await imagemin(['resources/img/*.{jpg,png}'], {
        destination: 'public/img',
        plugins: [
            imageminWebp({quality: 75})
        ]
    });
    console.log('Images optimized');
})();

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ])
    .version();

rimraf('resources/img/*', () => console.log('Deleted resources/img/*'));
