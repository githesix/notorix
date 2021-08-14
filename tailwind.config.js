const theme = require('tailwindcss/defaultTheme');
const defaultTheme = require('tailwindcss/defaultTheme');

const nxcolors = require('./tailwind.nxcolors');

module.exports = {
    mode: 'jit',
    purge: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './vendor/githesix/**/*.blade.php',
        '../packages/notorix-exim/resources/views/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/components/*.vue',
        './config/perso.php',
    ],

    theme: {
        extend: {
            colors: {
        'bleuis': nxcolors.bleuis,
        'orangis': nxcolors.orangis,
        'shamrock': nxcolors.shamrock,
        'verger': nxcolors.verger,
        'thevert': nxcolors.thevert,
        'fantomis': nxcolors.fantomis,
        'platine': nxcolors.platine,
        'rougis': nxcolors.rougis,
        'acier': nxcolors.acier,
        'bronze': nxcolors.bronze,
        /* Aliases ... */
        'primary': nxcolors.bleuis,
        'secondary': nxcolors.orangis,
        'alert': nxcolors.rougis
      },
            fontFamily: {
                'sans': ['Nunito', 'Raleway', 'system-ui', '-apple-system', 'BlinkMacSystemFont', "Segoe UI", 'Roboto', "Helvetica Neue", 'Arial', "Noto Sans", 'sans-serif', "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji"],
        'lobster': ['Lobster Two'],
            },
        },
    },

    variants: {
        extend: {
            opacity: ['disabled'],
        },
    },

    plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
};
