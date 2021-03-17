
window._ = require('lodash');
window.Popper = require('popper.js').default;
window.appName = document.head.querySelector('meta[name="app-name"]').content;
window.baseUrl = document.head.querySelector('meta[name="base-url"]').content;
window.token = document.head.querySelector('meta[name="token"]').content;
window.socketUrl = document.head.querySelector('meta[name="echo"]').content;
window.audioEmergency = document.head.querySelector('meta[name="emergency-audio-url"]').content;
window.audioKejadian = document.head.querySelector('meta[name="kejadian-audio-url"]').content;
window.ringBackTone = document.head.querySelector('meta[name="ringbacktone-audio-url"]').content;
window.wssVc = document.head.querySelector('meta[name="wss-vc"]').content;
window.instansi = document.head.querySelector('meta[name="instansi"]').content;
window.defaultLat = document.head.querySelector('meta[name="default-lat"]').content;
window.defaultLng = document.head.querySelector('meta[name="default-lng"]').content;
window.marineUrl = document.head.querySelector('meta[name="marine-url"]').content;
window.radarUrl = document.head.querySelector('meta[name="radar-url"]').content;
window.danaDesa = document.head.querySelector('meta[name="dana-desa"]').content;
window.mastumapel = document.head.querySelector('meta[name="mastumapel"]').content;
window.socketPrefix = document.head.querySelector('meta[name="socket-prefix"]').content;
window.hasVc = document.head.querySelector('meta[name="has-vc"]').content;
window.clientId = document.head.querySelector('meta[name="cid"]').content;
window.clientSecret = document.head.querySelector('meta[name="csc"]').content;
window.induk = document.head.querySelector('meta[name="induk"]').content;
window.danaDesa = document.head.querySelector('meta[name="dana-desa"]').content;
window.peneranganSatuan = document.head.querySelector('meta[name="penerangan-satuan"]').content;
window.visiMisi = document.head.querySelector('meta[name="visi-misi"]').content;
window.kegiatanBhabin = document.head.querySelector('meta[name="kegiatan-bhabin"]').content;

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */

try {
    window.$ = window.jQuery = require('jquery');

    require('bootstrap');
} catch (e) {}

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');
window.axios.defaults.baseURL = window.baseUrl + '/api/'

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

/*let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}*/

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

window.io = require("socket.io-client");

if (typeof io != "undefined") {
    window.Echo = null
}
// axios.interceptors.response.use(
// function(response) { return response; },
// function(error) {
//     if (error.response) {
//         if(error.response.status == 401) {
//         	axios.get(baseUrl + '/auth/logout')
// 		    .then(({ data }) => {
// 				location.reload()
// 			})
//         }
//         return Promise.reject(error);
//     }
// });
