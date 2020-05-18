import Vue from "vue";
import {ValidationProvider,ValidationObserver} from 'vee-validate'
import VueTheMask from 'vue-the-mask'
import Buefy from 'buefy'
Vue.use(Buefy,{
    defaultIconPack: 'fas',
    defaultIconNext:'chevron-right',
    defaultIconPrev:'chevron-left',
    defaultContainerElement: '#content',
})


Vue.use(VueTheMask)
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

Vue.component('register-form', require('./components/auth/registerForm').default);
Vue.component('login-form', require('./components/auth/loginForm').default);
Vue.component('forget-form', require('./components/auth/forgetForm').default);
Vue.component('nav-bar', require('./components/layout/navBar').default);
Vue.component('reset-password-form', require('./components/auth/resetPasswordForm').default);

Vue.component('user-list', require('./components/coordinator/users/userList').default);
Vue.component('user-detail', require('./components/coordinator/users/userDetail').default);

Vue.component('pending-transactions', require('./components/coordinator/pendingTransactions/pendingTransactionsList').default);

Vue.component('transaction-create', require('./components/operator/transaction/create').default)
