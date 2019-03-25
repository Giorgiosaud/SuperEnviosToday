<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <label for="currencyId">Seleccione el Operador</label>

        <v-select
          v-model="selectedUser"
          :options="foreign_users"
          label="name"
        />
      </div>
      <div class="col-12">
        <label for="currencyId">Seleccione el tipo de moneda</label>
        <v-select
          id="currencyId"
          v-model="selectedCurrency"
          :options="currencies"
          label="name"
        />
      </div>
      <div class="col-12">
        <label for="bankId">Seleccione el Banco</label>
        <v-select
          id="bankId"
          v-model="selectedBank"
          :options="banks"
          label="name"
        />
      </div>
      <div class="col-12">
        <label for="number">Número de Cuenta</label>
        <input
          id="number"
          v-model="number"
          type="text"
          class="input-base"
        >
      </div>
      <div class="col-12">
        <button
          type="button"
          class="btn btn-primary mt-2"
          @click.prevent="addAccount"
        >
          Registrar Cuenta
        </button>
      </div>
    </div>
  </div>
</template>

<script>
    /* eslint-disable no-alert */

    import axios from 'axios';

export default {
  name: 'AssignForeignAccount',
  data() {
    return {
      selectedUser: null,
      foreign_users: [],
      banks: [],
      currencies: [],
      selectedCurrency: null,
      selectedBank: null,
      number: '',
    };
  },
  watch: {
    selectedCurrency(val) {
      this.getBanks(val.id);
    },
  },
  created() {
    this.getForgeinUsers();
    this.getCurrencies();
  },
  methods: {
    getBanks(currencyId) {
      axios.get(`api/foreign_banks/${currencyId}`).then((response) => {
        this.banks = response.data;
      });
    },
    getCurrencies() {
      axios.get('api/foreign_currencies').then((response) => {
        this.currencies = response.data;
      });
    },
    addAccount() {
      axios.post('api/accounts', {
        user_id: this.selectedUser.id,
        bank_id: this.selectedBank.id,
        number: this.number,
        is_operator_account: true,
      })
        .then(() => {
          alert('account added');
          window.location.reload();
        });
    },
    getForgeinUsers() {
      axios.get('api/foreign_operators').then(({ data }) => {
        this.foreign_users = data;
      });
    },
  },
};
</script>

<style scoped>

</style>
