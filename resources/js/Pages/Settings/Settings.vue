<template>
  <div class="container">
    <currencies />
    <hr>
    <bank />
    <hr>
    <label
      class="label-base"
      for="venezuelan-tax"
    >Impuesto Venezuela</label>
    <input
      id="venezuelan-tax"
      v-model="venezuelaTax"
      type="text"
      class="input-base"
    >
    <button
      class="btn btn-primary"
      @click="saveTax"
    >
      Actualizar Impuesto
    </button>
    <hr>
    <status />
  </div>
</template>

<script>
import Bank from './Bank';
import Currencies from './Currencies';
import Status from './Status';

export default {
  name: 'Settings',
  components: {
    Bank,
    Currencies,
    Status,

  },
  computed: {
    venezuelaTax: {
      get() {
        return this.$store.state.settings.settings.venezuelanBankTax;
      },
      set(val) {
        return this.$store.commit('settings/SET_VENEZUELA_TAX', val);
      },
    },
  },
  created() {
    this.getCurrencies();
    this.getBanks();
    this.getSettings();
  },
  methods: {
    getSettings() {
      this.$store.dispatch('settings/GET_SETTINGS');
    },
    saveTax() {
      this.$store.dispatch('settings/SET_TAX', this.venezuelaTax);
    },
    getCurrencies() {
      this.$store.dispatch('settings/GET_CURRENCIES');
    },
    getBanks() {
      this.$store.dispatch('settings/GET_BANKS');
    },
  },
};
</script>

<style scoped>

</style>
