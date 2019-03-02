const state = {
    screenWidth: 0,
    currencies: []
};
const getters = {};
const actions = {
    GET_CURRENCIES(context) {
        window.axios.get('api/currencies').then((response) => {
            context.commit('SET_CURRENCIES', response.data);
        })
    }
};
const mutations = {
    SET_SCREEN_WIDTH(state, size) {
        state.screenWidth = size;
    },
    SET_CURRENCIES(state, currencies) {
        state.currencies = currencies;
    }
};
export default {
    namespaced: true,
    state,
    getters,
    actions,
    mutations
}
