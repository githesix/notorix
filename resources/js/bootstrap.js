window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.moment = require('moment');
window.moment.locale('fr');

import { Notyf } from 'notyf';
window.Notyf = Notyf;

window.Pikaday = require('pikaday');

window.Swal = require('sweetalert2');

// Mixin Popup confirmation
window.Swconfirme = Swal.mixin({
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#3581B8',
    cancelButtonColor: '#6E633D',
    /* confirmButtonText: 'Confirmer',
    cancelButtonText: 'Annuler', */
});

window.toggledd = function(myDropMenu) {
    document.getElementById(myDropMenu).classList.toggle("invisible");
}

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo';

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
