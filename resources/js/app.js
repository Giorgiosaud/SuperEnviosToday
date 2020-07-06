import Vue from 'vue'
require('./bootstrap');
import './vue-fontawesome-config';
import './components';
import './vee-validate-config'
import * as VeeValidate from 'vee-validate'
import es from 'vee-validate/dist/locale/es';
import 'froala-editor/js/plugins.pkgd.min.js';
import 'froala-editor/js/third_party/embedly.min';
import 'froala-editor/js/third_party/font_awesome.min';
import 'froala-editor/js/third_party/spell_checker.min';
import 'froala-editor/js/third_party/image_tui.min';
import 'froala-editor/css/froala_editor.pkgd.min.css';

// Import and use Vue Froala lib.
import VueFroala from 'vue-froala-wysiwyg'
Vue.use(VueFroala)
// Install the Plugin.
Vue.use(VeeValidate);
VeeValidate.localize('es',es);
// Localize takes the locale object as the second argument (optional) and merges it.
const app=new Vue({
    el:'#app',
    data:{
        navbarOpen:false,
        reduced:true,
    }
});
