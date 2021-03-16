import Vue from 'vue'
import App from '@/App'
import router from '@/router'
import store from '@/store'
import VueToast from 'vue-toast-notification';
import 'vue2-datepicker/locale/id'
import 'vue2-datepicker/index.css'
import DatePicker from 'vue2-datepicker'
import BootstrapVue from 'bootstrap-vue'
import PhosporIcon from 'phosphor-vue'
import ToggleButton from 'vue-js-toggle-button'
import PerfectScrollbar from 'vue2-perfect-scrollbar'
import * as VueGoogleMaps from 'vue2-google-maps'
import Multiselect from 'vue-multiselect'
 

require('./bootstrap');

Vue.use(BootstrapVue)
Vue.use(DatePicker)
Vue.use(VueToast, {
    position: 'top-right',
})
Vue.use(PhosporIcon)
Vue.use(ToggleButton)
Vue.use(PerfectScrollbar)
Vue.use(VueGoogleMaps, {
    load: {
        libraries: 'geometry',
        key: 'AIzaSyB25fSPA0inHhn366JMbbuR5E1T5ld92EQ',
    }
})

Vue.component('multiselect', Multiselect)
Vue.component('App', App)

const app = new Vue({
    store,
    router,
    el: '#app',
    template: '<App/>'
});