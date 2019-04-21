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
        >Ingrese nombre de moneda:</label>
        <input
            id="name"
            v-model="name"
            name="Nombre de moneda"
            type="text"
            v-validate="'required'"
            class="input-base"
        >
          <span
              class="text-danger"
              v-if="errors.has('Nombre de moneda')"
          >{{ errors.first('Nombre de moneda') }}</span>
      </div>
      <div class="col-12">
        <label
          for="identificator"
          class="label-base bg-white"
        >Ingrese Identificador:</label>
        <input
            id="identificator"
            v-model="identificator"
            name="Identificador"
            v-validate="'required'"
            type="text"
            class="input-base"
        >
          <span
              class="text-danger"
              v-if="errors.has('Identificador')"
          >{{ errors.first('Identificador') }}</span>
      </div>
      <div class="col-12">
        <label
          for="sign"
          class="label-base bg-white"
        >Ingrese Signo:</label>
        <input
            id="sign"
            v-model="sign"
            name="Signo"
            v-validate="'required'"
            type="text"
            class="input-base"
        >
          <span
              class="text-danger"
              v-if="errors.has('Signo')"
          >{{ errors.first('Signo') }}</span>
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
        this.$validator.validate().then((valid) => {
            if (valid) {
                this.$store.dispatch('settings/CREATE_NEW_CURRENCY', {
                    name: this.name,
                    identificator: this.identificator,
                    sign: this.sign,
                }).then(() => {
                    this.bankName = '';
                    this.selectedCurrency = '';
                });
            }
      });
    },
  },
};
</script>

<style scoped>

</style>
