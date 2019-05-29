<template>
  <div class="container">
    <div class="row py-2">
      <h1>Transaccion de ajuste</h1>
    </div>
    <div class="row py-2">
      <label
        for="selector-operador"
        class="base-label col-12"
      >Seleccione Operador</label>
      <v-select
        id="selector-operador"
        v-model="selectedOperator"
        :options="operators"
        class="col-12 p-0"
        label="name"
      >
        <template
          slot="option"
          slot-scope="option"
        >
          {{ option.name }} {{ option.last_name }}
        </template>
      </v-select>
    </div>

    <div class="row py-2">
      <label
        for="selector-moneda"
        class="base-label col-12"
      >
        Seleccione Moneda
      </label>
      <v-select
        id="selector-moneda"
        v-model="selectedCurrency"
        :options="currencies"
        :disabled="!selectedOperator"
        class="col-12 p-0"
        label="name"
      />
    </div>
    <div class="row py-2">
      <label
        for="selector-cuenta"
        class="base-label col-12"
      >
        Seleccione Cuenta A Ajustar
      </label>
      <v-select
        id="selector-cuenta"
        v-model="selectedAccount"
        :options="accounts"
        :disabled="!selectedCurrency"
        :get-option-label="selectedAccountLabel"
        class="col-12 p-0"
      >
        <template
          slot="option"
          slot-scope="option"
        >
          {{ option.bank.name }} – {{ option.number }}
        </template>
      </v-select>
    </div>
    <div class="row py-2">
      <label
        for="selector-tipo-ajuste"
        class="base-label col-12"
      >Seleccione Tipo de Ajuste</label>
      <v-select
        id="selector-tipo-ajuste"
        v-model="selectedTransactionType"
        :options="transactionTypes"
        class="col-12 p-0"
        :clearable="false"
        label="name"
        index="value"
      />
    </div>
    <div class="row py-2">
      <label
        for="amount"
        class="base-label col-12"
      >
        Ingrese Monto
      </label>
      <input
        id="amount"
        v-model="amount"
        type="text"
        class="input-base"
      >
    </div>
    <div class="row py-2">
      <button
        class="btn btn-primary"
        @click.prevent="addTransaction"
      >
        Agregar Transaccion de Ajuste
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'FixTransaction',
  data() {
    return {
      selectedOperator: null,
      transactionTypes: [{
        name: 'Retiro',
        value: 'outcome',
      },
      {
        name: 'Deposito',
        value: 'income',
      }],
      operators: [],
      selectedCurrency: null,
      selectedAccount: null,
      selectedTransactionType: null,
      newAccountNumber: '',
      amount: '',
    };
  },
  computed: {
    accounts() {
      if (this.selectedOperator) {
        return this.selectedOperator.accounts
          .filter(account => account.bank.currency.name === this.selectedCurrency);
      }
      return [];
    },
    currencies() {
      if (this.selectedOperator) {
        return [...new Set(this.selectedOperator.accounts.map(acc => acc.bank.currency.name))];
      }
      return [];
    },
  },
  watch: {
    selectedOperator() {
      this.selectedCurrency = null;
    },
    selectedCurrency() {
      this.selectedAccount = null;
    },
  },
  created() {
    this.getOperators();
  },
  methods: {
    selectedAccountLabel(option) {
      return `${option.bank.name} – ${option.number}`;
    },
    getOperators() {
      window.axios.get('api/operators').then((response) => {
        this.operators = response.data;
      });
    },
    addTransaction() {
      window.axios.post('api/adjust-transaction', {
        to_account_id: this.selectedAccount.id,
        to_user_id: this.selectedOperator.id,
        type: this.selectedTransactionType,
        amount: this.amount,
      }).then(() => {
        this.amount = '';
        this.selectedOperator = null;
        this.selectedCurrency = null;
        this.selectedTransactionType = null;
        this.selectedAccount = null;
        alert('monto añadido exitosamente');
      });
    },
  },
};
</script>

<style scoped>
</style>
