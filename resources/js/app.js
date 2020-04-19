import Vue from 'vue'
require('./bootstrap');
import './vue-fontawesome-config';
import './components';
import './vee-validate-config'
import * as VeeValidate from 'vee-validate'
import es from 'vee-validate/dist/locale/es';
// Install the Plugin.
Vue.use(VeeValidate);
VeeValidate.localize('es',es);
// Localize takes the locale object as the second argument (optional) and merges it.
const app=new Vue({
    el:'#app'
});
