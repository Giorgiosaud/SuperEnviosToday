import store from './store';
import vco from 'v-click-outside'
import vSelect from 'vue-select'
import Datetime from 'vue-datetime'
import 'vue-datetime/dist/vue-datetime.css'
import VueFrappe from 'vue2-frappe';
import { Settings } from 'luxon'
import VueCurrencyFilter from 'vue-currency-filter'
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');
window.Vue = require('vue');
Vue.use(vco);

Settings.defaultLocale = 'es'
Vue.use(Datetime);
Vue.use(VueFrappe);
Vue.use(VueCurrencyFilter,
    {
        symbol : ' Bs',
        thousandsSeparator: '.',
        fractionCount: 2,
        fractionSeparator: ',',
        symbolPosition: 'front',
        symbolSpacing: true
    })
/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))
Vue.component('v-select', vSelect);
Vue.component('main-menu', require('./components/mainMenu.vue').default);
Vue.component('register-member', require('./components/registerMember.vue').default);
Vue.component('users-list', require('./components/usersList.vue').default);
Vue.component('rate', require('./components/rate.vue').default);


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
const app = new Vue({
    el: '#app',
    store,
    mounted(){
    this.$store.commit('globals/SET_SCREEN_WIDTH', window.innerWidth);
     window.addEventListener("resize", _.debounce(()=> {
        this.$store.commit('globals/SET_SCREEN_WIDTH', window.innerWidth);
    },500));
    }
});
