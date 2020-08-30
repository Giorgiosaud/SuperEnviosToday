import Vue from 'vue';
import './vue-fontawesome-config';
import './components';
import './vee-validate-config';
import * as VeeValidate from 'vee-validate';
import es from 'vee-validate/dist/locale/es.json';
import VueQuillEditor from 'vue-quill-editor'

import 'quill/dist/quill.core.css' // import styles
import 'quill/dist/quill.snow.css' // for snow theme
import 'quill/dist/quill.bubble.css' // for bubble theme

require('./bootstrap');

// Install the Plugin.
Vue.use(VueQuillEditor, {
  modules: {
    toolbar: [
      [{header: [1, 2, false]}],
      ['bold', 'italic', 'underline'],
      ['image', 'code-block']
    ],
  },
  theme: 'snow'


})
Vue.use(VeeValidate);
VeeValidate.localize('es', es);
// Localize takes the locale object as the second argument (optional) and merges it.
// eslint-disable-next-line no-new
Vue.mixin({
  methods: {
    changedFilter(filters) {
      const filterKeys = Object.keys(filters)
      filterKeys.forEach((filter) => {
        if (filters[filter] === '') {
          delete filters[filter];
        }
      });
      this.filters = filters;
      this.loadAsyncData();
    },
    getAllUrlParams(url) {
      var queryString = url ? url.split('?')[1] : window.location.search.slice(1);
      var obj = {};
      if (queryString) {
        queryString = queryString.split('#')[0];
        var arr = queryString.split('&');
        for (var i = 0; i < arr.length; i++) {
          var a = arr[i].split('=');
          var paramName = a[0];
          var paramValue = typeof (a[1]) === 'undefined' ? true : a[1];
          paramName = paramName.toLowerCase();
          if (typeof paramValue === 'string') paramValue = paramValue.toLowerCase();
          if (paramName.match(/\[(\d+)?\]$/)) {
            var key = paramName.replace(/\[(\d+)?\]/, '');
            if (!obj[key]) obj[key] = [];
            if (paramName.match(/\[\d+\]$/)) {
              var index = /\[(\d+)\]/.exec(paramName)[1];
              obj[key][index] = paramValue;
            } else {
              obj[key].push(paramValue);
            }
          } else {
            if (!obj[paramName]) {
              obj[paramName] = paramValue;
            } else if (obj[paramName] && typeof obj[paramName] === 'string') {
              obj[paramName] = [obj[paramName]];
              obj[paramName].push(paramValue);
            } else {
              obj[paramName].push(paramValue);
            }
          }
        }
      }
      return obj;
    },
  }
})
new Vue({
  el: '#app',
  data: {
    navbarOpen: false,
    reduced: true,
  },
});
