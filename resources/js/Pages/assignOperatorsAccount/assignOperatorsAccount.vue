<template>
  <div class="container">
    <div class="row">
      <h1>Agregar Cuentas a Operadores</h1>
    </div>
    <div class="row">
      <div class="col-12">
        <label for="currencyId">Seleccione el Operador</label>

        <v-select
          v-model="selectedOperator"
          v-validate="'required'"
          :options="foreign_users"
          :clearable="false"
          label="name"
          name="Usuario"
        />
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Usuario') }}</strong>
        </span>
      </div>
      <div class="col-12">
        <label for="currencyId">Seleccione el tipo de moneda</label>
        <v-select
          id="currencyId"
          v-model="selectedCurrency"
          v-validate="'required'"
          :options="currencies"
          :clearable="false"
          :disabled="disableCurrencySelector"
          label="name"
        />
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Moneda') }}</strong>
        </span>
      </div>
      <div class="col-12">
        <label for="bankId">Seleccione el Banco</label>
        <v-select
          id="bankId"
          v-model="selectedBank"
          :options="banks"
          :clearable="false"
          label="name"
          name="Banco"
        />
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Banco') }}</strong>
        </span>
      </div>
      <div class="col-12">
        <label for="number">Número de Cuenta</label>
        <input
          id="number"
          v-model="number"
          v-validate="customAccountValidation"
          type="text"
          class="input-base"
          name="Número de Cuenta"
        >
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Número de Cuenta') }}</strong>
        </span>
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
  name: 'AssignOperatorAccount',
  data() {
    return {
      selectedOperator: null,
      foreign_users: [],
      banks: [],
      currencies: [],
      selectedCurrency: null,
      selectedBank: null,
      number: '',
      disableCurrencySelector: false,
    };
  },
  computed: {
    customAccountValidation() {
      if (this.selectedCurrency && this.selectedCurrency.identificator === 'Bs') {
        return 'required|length:20';
      }
      return 'required';
    },
  },
  watch: {
    selectedCurrency(val) {
      this.getBanks(val.id);
      this.selectedBank = null;
    },
    selectedOperator(val) {
      if (val.roles.length === 1) {
        if (val.roles.some(role => role.name_id === 'venezuelan_operator')) {
          this.selectedCurrency = this.currencies.find(curr => curr.identificator === 'Bs');
          this.disableCurrencySelector = true;
        } else {
          this.disableCurrencySelector = false;
        }
      }
    },
  },

  created() {
    this.getForgeinUsers();
    this.getCurrencies();
  },
  methods: {
    getBanks(currencyId) {
      axios.get(`api/country_banks/${currencyId}`).then((response) => {
        this.banks = response.data;
      });
    },
    getCurrencies() {
      axios.get('api/currencies').then((response) => {
        this.currencies = response.data;
      });
    },
    addAccount() {
      axios.post('api/operator-account', {
        user_id: this.selectedOperator.id,
        bank_id: this.selectedBank.id,
        number: this.number,
      })
        .then(() => {
          alert('cuenta añadida exitosamente');
          this.selectedOperator = null;
          this.selectedCurrency = null;
          this.selectedBank = null;
          this.number = '';
        });
    },
    getForgeinUsers() {
      axios.get('api/operators').then(({ data }) => {
        this.foreign_users = data;
      });
    },
  },
};
</script>

<style scoped>

</style>
