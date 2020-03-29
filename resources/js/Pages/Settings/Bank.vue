<template>
  <div>
    <h1>Lista de Bancos</h1>
    <div class="table-responsive">
      <table
        v-if="banks.length"
        class="table"
      >
        <tr>
          <th>Nombre</th>
          <th>Moneda</th>
        </tr>
        <tr
          v-for="(bank , bankId) in banks"
          :key="bankId"
        >
          <td>{{ bank.name }}</td>
          <td>{{ bank.currency.name }}</td>
        </tr>
      </table>
      <h3 v-else>
        No posee Bancos Registrados
      </h3>
    </div>
    <h2>Agregar Bancos</h2>
    <form>
      <div class="col-12">
        <label
          for="bank-name"
          class="label-base bg-white"
        >Ingrese nombre de banco:</label>
        <input
          id="bank-name"
          v-model="bankName"
          v-validate="'required'"
          class="input-base"
          name="Nombre de Banco"
          type="text"
        >
        <span
          v-if="errors.has('Nombre de Banco')"
          class="text-danger"
        >{{ errors.first('Nombre de Banco') }}</span>
      </div>
      <div class="col-12">
        <label
          for="currency"
          class="label-base bg-white"
        >Seleccione Moneda:</label>
        <v-select
          id="currency"
          v-model="selectedCurrency"
          v-validate="'required'"
          :options="currencies"
          :searchable="false"
          class="input-base"
          label="name"
          name="Tipo de Moneda"
        />
        <span
          v-if="errors.has('Tipo de Moneda')"
          class="text-danger"
        >{{ errors.first('Tipo de Moneda') }}</span>
      </div>
      <div class="col-12">
        <button
          class="btn-primary"
          @click.prevent="createBank"
        >
          Guardar
        </button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'Banks',
  data() {
    return {
      bankName: '',
      selectedCurrency: '',
    };
  },
  computed: {
    banks() {
      return this.$store.state.settings.banks;
    },
    currencies() {
      return this.$store.state.settings.currencies;
    },
  },
  methods: {
    createBank() {
      this.$validator.validate().then((valid) => {
        if (valid) {
          this.$store.dispatch('settings/CREATE_NEW_BANK', {
            name: this.bankName,
            currency_id: this.selectedCurrency.id,
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
