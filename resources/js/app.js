import Vue from 'vue';
import './vue-fontawesome-config';
import './components';
import './vee-validate-config';
import * as VeeValidate from 'vee-validate';
import es from 'vee-validate/dist/locale/es.json';
/*import 'froala-editor/js/plugins.pkgd.min';
import 'froala-editor/js/third_party/embedly.min';
import 'froala-editor/js/third_party/font_awesome.min';
import 'froala-editor/js/third_party/spell_checker.min';
import 'froala-editor/js/third_party/image_tui.min';
import 'froala-editor/css/froala_editor.pkgd.min.css';
*/
// Import and use Vue Froala lib.
import VueFroala from 'vue-froala-wysiwyg';

require('./bootstrap');

Vue.use(VueFroala);
// Install the Plugin.
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
