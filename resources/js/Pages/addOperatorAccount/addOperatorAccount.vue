<template>
  <div class="container">
    <div class="row">
      <h1>Agregar Cuentas</h1>
    </div>
    <div class="row">
      <div class="col-12">
        <label for="currencyId">Seleccione el tipo de moneda</label>
        <v-select
          id="currencyId"
          v-model="selectedCurrency"
          v-validate="'required'"
          name="currency_id"
          :options="currencies"
          :clearable="false"
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
      <div
        v-if="selectedCurrency && selectedCurrency.identificator==='BsS'"
        class="col-12"
      >
        <label for="bankId">Seleccione el Tipo de Cuenta</label>
        <v-select
          id="bankId"
          v-model="selectedAccountType"
          :options="['ahorro','corriente']"
          name="Tipo de Cuenta"
        />
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
  name: 'AddOperatorAccount',
  data() {
    return {
      selectedAccountType: null,
      banks: [],
      currencies: [],
      selectedCurrency: null,
      selectedBank: null,
      number: '',
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
  },

  created() {
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
      axios.post('api/add-account', {
        bank_id: this.selectedBank.id,
        type: this.selectedAccountType,
        number: this.number,
      })
        .then(() => {
          alert('cuenta añadida exitosamente');
          this.selectedOperator = null;
          this.selectedCurrency = null;
          this.selectedBank = null;
          this.selectedAccountType = null;
          this.number = '';
        });
    },
  },
};
</script>
