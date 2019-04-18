/* eslint-disable no-param-reassign */
const state = {
  currencies: [],
  banks: [],
  settings: {
    venezuelanBankTax: 2,
    status: true,
  },
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
  GET_SETTINGS(context) {
    return window.axios.get('api/settings').then((response) => {
      context.commit('SET_SETTINGS', response.data);
    });
  },
  SET_TAX(cont, pay) {
    return window.axios.post('api/setting_tax', { value: pay })
      .then((response) => {
        cont.dispatch('GET_SETTINGS', response.data);
      });
  },
  CREATE_NEW_BANK(context, payload) {
    return axios.post('api/banks', payload)
      .then(() => {
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
  // eslint-disable-next-line
  SET_BANKS(stateX, banks) {
    stateX.banks = banks;
  },
  SET_VENEZUELA_TAX(stateX, tax) {
    stateX.settings.venezuelanBankTax = parseFloat(tax.replace(',', '.'));
  },
  SET_CURRENCIES(stateX, currencies) {
    stateX.currencies = currencies;
  },
  SET_SETTINGS(stateX, settings) {
    stateX.settings.venezuelanBankTax = parseFloat(settings.find(s => s.key === 'venezuelanBankTax').value.replace(',', '.'));
    stateX.settings.status = parseFloat(settings.find(s => s.key === 'status').value) === 1;
  },
};
export default {
  namespaced: true,
  state,
  getters,
  actions,
  mutations,
};
