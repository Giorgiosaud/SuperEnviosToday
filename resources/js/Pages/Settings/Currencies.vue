<template>
  <div>
    <h1>Lista de Monedas</h1>
    <div class="table-responsive">
      <table
        v-if="currencies.length"
        class="table"
      >
        <tr>
          <th>Nombre</th>
          <th>Identificador</th>
          <th>Signo</th>
        </tr>
        <tr v-for="currency in currencies">
          <td>{{ currency.name }}</td>
          <td>{{ currency.identificator }}</td>
          <td>{{ currency.sign }}</td>
        </tr>
      </table>
      <h3 v-else>
        No Monedas Registradas
      </h3>
    </div>
    <h2>Agregar Moneda</h2>
    <form>
      <div class="col-12">
        <label
          for="name"
          class="label-base bg-white"
        >Ingrese nombre de banco:</label>
        <input
          id="name"
          v-model="name"
          type="text"
          class="input-base"
        >
      </div>
      <div class="col-12">
        <label
          for="identificator"
          class="label-base bg-white"
        >Ingrese nombre de banco:</label>
        <input
          id="identificator"
          v-model="identificator"
          type="text"
          class="input-base"
        >
      </div>
      <div class="col-12">
        <label
          for="sign"
          class="label-base bg-white"
        >Ingrese nombre de banco:</label>
        <input
          id="sign"
          v-model="sign"
          type="text"
          class="input-base"
        >
      </div>
      <div class="col-12">
        <button
          class="btn-primary"
          @click.prevent="createCurrency"
        >
          Guardar
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'Currencies',
  data() {
    return {
      name: '',
      identificator: '',
      sign: '',
    };
  },
  computed: {
    currencies() {
      return this.$store.state.settings.currencies;
    },
  },
  methods: {
    createCurrency() {
      this.$store.dispatch('settings/CREATE_NEW_CURRENCY', {
        name: this.name,
        identificator: this.identificator,
        sign: this.sign,
      }).then(() => {
        this.bankName = '';
        this.selectedCurrency = '';
      });
    },
  },
};
</script>

<style scoped>

</style>
