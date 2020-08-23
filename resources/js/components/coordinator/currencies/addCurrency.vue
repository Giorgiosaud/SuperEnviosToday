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
        <b-field label="Indentificador">
          <b-input
            v-model="identifier"
            type="text"
            placeholder="Identificador de moneda"
            required />
        </b-field>
        <b-field label="Sign">
          <b-input
            v-model="sign"
            type="text"
            placeholder="Signo de moneda"
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
