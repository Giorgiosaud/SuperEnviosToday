const state = {
  currencies: [],
  banks: [],
};
const getters = {};
const actions = {
  GET_CURRENCIES(context) {
    return window.axios.get('api/currencies').then((response) => {
      context.commit('SET_CURRENCIES', response.data);
    });
  },
  GET_BANKS(context) {
    return window.axios.get('api/banks').then((response) => {
      context.commit('SET_BANKS', response.data);
    });
  },
  CREATE_NEW_BANK(context, payload) {
    return window.axios.post('api/banks', payload).then(() => {
      context.dispatch('GET_BANKS');
    });
  },
  CREATE_NEW_CURRENCY(context, payload) {
    return window.axios.post('api/currency', payload).then(() => {
      context.dispatch('GET_BANKS');
    });
  },
};
const mutations = {
  SET_BANKS(state, banks) {
    state.banks = banks;
  },
  SET_CURRENCIES(state, currencies) {
    state.currencies = currencies;
  },
};
export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
