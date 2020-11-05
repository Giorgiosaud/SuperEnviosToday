<template>
  <div>
    <div
      class="modal-card"
      style="width: auto">
      <header class="modal-card-head">
        <p class="modal-card-title">
          Agregue un Banco
        </p>
      </header>
      <section class="modal-card-body">
        <b-field label="Nombre">
          <b-input
            v-model="name"
            type="text"
            placeholder="Nombre de Moneda"
            required />
        </b-field>
        <b-field :label="$t('currencies.CURRENCY')">
          <b-input
            v-model="identifier"
            type="text"
            :placeholder="$t('currencies.CURRENCY')"
            required />
        </b-field>
        <b-field :label="$t('currencies.SIGN')">
          <b-input
            v-model="sign"
            type="text"
            :placeholder="$t('currencies.SIGN')"
            required />
        </b-field>
        <div class="field">
            <b-checkbox v-model="format_with_symbol">{{$t('currencies.FORMATWITHSYMBOL')}}</b-checkbox>
        </div>
        <b-field :label="$t('currencies.DECIMAL:SEPARATOR')">
          <b-input
            v-model="decimal"
            type="text"
            :placeholder="$t('currencies.DECIMAL:SEPARATOR')"
            required />
        </b-field>
        <b-field :label="$t('currencies.THOUSAND:SEPARATOR')">
          <b-input
            v-model="separator"
            type="text"
            :placeholder="$t('currencies.THOUSAND:SEPARATOR')"
            required />
        </b-field>
        <b-field :label="$t('currencies.PRECISION')">
          <b-input
            v-model="precision"
            type="text"
            :placeholder="$t('currencies.PRECISION')"
            required />
        </b-field>
      </section>
      <footer class="modal-card-foot">
        <button
          class="button"
          type="button"
          @click="$parent.close()"
          @keypress.esc="$parent.close()">
          Cerrar
        </button>
        <button
          class="button is-primary"
          @click="newCurrency"
          @keypress.enter="newCurrency">
          Guardar
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddCurrency',
  data: () => ({
    name: '',
    identifier: '',
    sign: '',
    separator: '.',
    format_with_symbol: false,
    decimal: ',',
    precision: 0,
    savingCurrency: false,
  }),
  methods: {
    async newCurrency() {
      this.savingCurrency = true;

      try {
        await $http.post('api/currencies', {
          name: this.name,
          identifier: this.identifier,
          sign: this.sign,
          separator: this.separator,
          format_with_symbol: this.format_with_symbol,
          decimal: this.decimal,
          precision: this.precision,
        });
      } finally {
        this.savingCurrency = false;
        this.$parent.close();
        this.$emit('currency-created');
      }
    },
  },
};
</script>

<style scoped>

</style>
