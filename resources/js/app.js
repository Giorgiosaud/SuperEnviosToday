import Vue from 'vue';
import './vue-fontawesome-config';
import './components';
import './vee-validate-config';
import * as VeeValidate from 'vee-validate';
import es from 'vee-validate/dist/locale/es.json';
import VueQuillEditor from 'vue-quill-editor';
import i18n from './i18n';
import 'quill/dist/quill.core.css'; // import styles
import 'quill/dist/quill.snow.css'; // for snow theme
import 'quill/dist/quill.bubble.css'; // for bubble theme
import filtersMixin from './filtersMixin';

require('./bootstrap');

// Install the Plugin.
Vue.use(VueQuillEditor, {
  modules: {
    toolbar: [
      [{ header: [1, 2, false] }],
      ['bold', 'italic', 'underline'],
      ['image', 'code-block'],
    ],
  },
  theme: 'snow',

});
Vue.use(VeeValidate);
VeeValidate.localize('es', es);
Vue.mixin(filtersMixin);
// Localize takes the locale object as the second argument (optional) and merges it.
// eslint-disable-next-line no-unused-vars
const app = new Vue({
  el: '#app',
  i18n,
  data: {
    navbarOpen: false,
    reduced: true,
  },
});
