import Vue from 'vue';
import Vuex from 'vuex'
import transactions from './modules/transactions'
import globals from './modules/globals'
Vue.use(Vuex)
const debug = process.env.NODE_ENV !== 'production'
export default new Vuex.Store({
  modules: {
    transactions,
    globals
  },
  strict: debug,
});

