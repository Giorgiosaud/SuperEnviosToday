import Vue from 'vue';
import { ValidationProvider, ValidationObserver } from 'vee-validate';
import VueTheMask from 'vue-the-mask';
import Buefy from 'buefy';
import registerForm from './components/auth/registerForm.vue';
import loginForm from './components/auth/loginForm.vue';
import forgetForm from './components/auth/forgetForm.vue';
import resetPasswordForm from './components/auth/resetPasswordForm.vue';
import navBar from './components/layout/navBar.vue';
import usersList from './components/coordinator/users/usersList.vue';
import userDetail from './components/coordinator/users/userDetail.vue';
import banksList from './components/coordinator/banks/banksList.vue';
import accountsList from './components/coordinator/accounts/accountsList.vue';
import accountsDetails from './components/coordinator/accounts/accountsDetails.vue';
import currenciesList from './components/coordinator/currencies/currenciesList.vue';
import settingsList from './components/coordinator/settings/settingsList.vue';
import ratesList from './components/coordinator/rates/ratesList.vue';
import pendingTransactionsList from './components/coordinator/pendingTransactions/pendingTransactionsList.vue';
import MyPendingTransactionsList from './components/coordinator/pendingTransactions/myPendingTransactionsList.vue';
import create from './components/operator/transaction/create.vue';
import reviewTransaction from './components/operator/transaction/reviewTransaction.vue';
import transactions from './components/coordinator/transactions/transactions.vue';
import myTransactions from './components/operator/transactions/myTransactions.vue';
import myVenezuelanTransactions from './components/operator/transactions/myVenezuelanTransactions.vue';

Vue.use(Buefy, {
  defaultIconPack: 'fas',
  defaultIconNext: 'chevron-right',
  defaultIconPrev: 'chevron-left',
  defaultContainerElement: '#content',
});

Vue.use(VueTheMask);
Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);

Vue.component('register-form', registerForm);
Vue.component('login-form', loginForm);
Vue.component('forget-form', forgetForm);
Vue.component('reset-password-form', resetPasswordForm);
Vue.component('nav-bar', navBar);

Vue.component('users-list', usersList);
Vue.component('user-detail', userDetail);

Vue.component('banks-list', banksList);
Vue.component('accounts-list', accountsList);
Vue.component('accounts-details', accountsDetails);

Vue.component('currencies-list', currenciesList);
Vue.component('settings-list', settingsList);
Vue.component('rates-list', ratesList);

Vue.component('pending-transactions-list', pendingTransactionsList);
Vue.component('my-pending-transactions-list', MyPendingTransactionsList);

Vue.component('transaction-create', create);
Vue.component('review-transaction', reviewTransaction);
Vue.component('transactions', transactions);
Vue.component('my-transactions', myTransactions);
Vue.component('my-venezuelan-transactions', myVenezuelanTransactions);
