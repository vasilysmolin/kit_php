const mix = require('laravel-mix');
const imagemin = require('imagemin');
const imageminWebp = require('imagemin-webp');
const imageminSvgo = require('imagemin-svgo');
const {extendDefaultPlugins} = require('svgo');
const rimraf = require('rimraf');

(async () => {
    await imagemin(['resources/img/*.{jpg,png}'], {
        destination: 'public/img',
        plugins: [
            imageminWebp({quality: 75})
        ]
    });
    console.log('Webp converted');
})();

(async () => {
    await imagemin(['resources/img/*.svg'], {
        destination: 'public/img',
        plugins: [
            imageminSvgo({
                plugins: extendDefaultPlugins([])
            })
        ]
    });
    console.log('SVG optimized');
})();

mix.js('resources/js/app.js', 'public/js')
    .postCss('resources/css/app.css', 'public/css', [
        require('tailwindcss'),
    ])
    .version();

rimraf('resources/img/*', () => console.log('Deleted resources/img/*'));
