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
        name="Impuesto Venezuela"
        class="input-base"
        v-validate="{required:true,regex:/\d*\,?\.?\d+?/}"
    >
      <span
          class="text-danger"
          v-if="errors.has('Impuesto Venezuela')"
      >{{ errors.first('Impuesto Venezuela') }}</span>
    <button
      class="btn btn-primary"
      @click="saveTax"
    >
      Actualizar Impuesto
    </button>
    <hr>
      <!--status /-->
  </div>
</template>

<script>
    import Bank from './Bank';
    import Currencies from './Currencies';
    // import Status from './Status';

export default {
  name: 'Settings',
  components: {
    Bank,
    Currencies,
      // Status,

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
        this.$validator.validate().then((valid) => {
            if (valid) {
                this.$store.dispatch('settings/SET_TAX', this.venezuelaTax);
            }
        });
    },
    getCurrencies() {
      this.$store.dispatch('settings/GET_CURRENCIES');
    },
    getBanks() {
      this.$store.dispatch('settings/GET_BANKS');
    },
  },
    //TODO seleccione la moneda base
};
</script>

<style scoped>

</style>
