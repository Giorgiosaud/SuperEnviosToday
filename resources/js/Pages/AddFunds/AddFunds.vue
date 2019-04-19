<template>
  <div class="container">
    <div class="row py-2">
      <h1>Agregar Fondos a Operador Venezuela</h1>
    </div>
    <div class="row py-2">
      <label
        for="selector-operador-venezuela"
        class="base-label col-12"
      >Seleccione Operador Venezuela</label>
      <v-select
        id="selector-operador-venezuela"
        v-model="selectedOperator"
        :options="venezuelanOperators"
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
        for="selector-cuenta-venezuela"
        class="base-label col-12"
      >
        Seleccione Cuenta Destino
      </label>
      <v-select
        id="selector-cuenta-venezuela"
        v-model="selectedAccount"
        :options="accounts"
        :disabled="!selectedOperator"
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
      <button
        :disabled="!selectedOperator"
        class="btn btn-primary"
        @click.prevent="showAddAccountModal"
      >
        Agregar Cuenta
      </button>
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
        @click.prevent="addFunds"
      >
        Agregar Fondos
      </button>
    </div>
    <div
      id="addAccountModal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
    >
      <div
        class="modal-dialog modal-lg"
        role="document"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Agregar Cuenta
            </h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row">
                <v-select
                  id="selector-operador-venezuela"
                  v-model="selectedBank"
                  :options="banks"
                  class="col-12 p-0"
                  index="id"
                  label="name"
                >
                  <template
                    slot="option"
                    slot-scope="option"
                  >
                    {{ option.name }} – {{ option.currency.name }}
                  </template>
                </v-select>
              </div>
              <div class="row">
                <label
                  for="accountNumber"
                  class="label-base"
                >Account Number</label>
                <input
                  id="accountNumber"
                  v-model="newAccountNumber"
                  type="text"
                  class="input-base"
                >
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-primary"
              @click.prevent="addAccount"
            >
              Asociar Cuenta
            </button>
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
              @click.prevent="closeAddAccount"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddFunds',
  data() {
    return {
      selectedOperator: null,
      venezuelanOperators: [],
      banks: [],
      selectedBank: null,
      selectedAccount: null,
      newAccountNumber: '',
      amount: '',
    };
  },
  computed: {
    accounts() {
      if (this.selectedOperator) {
        return this.selectedOperator.accounts;
      }
      return [];
    },
  },
  created() {
    this.getAccounts();
  },
  methods: {
    selectedAccountLabel(option) {
      return `${option.bank.name} – ${option.number}`;
    },
    getAccounts() {
      window.axios.get('api/operadores-venezuela').then((response) => {
        this.venezuelanOperators = response.data;
      });
    },
    showAddAccountModal() {
      this.getBanks();
      $('#addAccountModal').modal('show');
    },
    addAccount() {
      window.axios.post('api/accounts', {
        user_id: this.selectedOperator.id,
        is_operator_account: true,
        bank_id: this.selectedBank,
        number: this.newAccountNumber,
      }).then(() => {
        this.selectedOperator = null;

        this.getAccounts();
      });
      this.closeAddAccount();
    },
    closeAddAccount() {
      $('#addAccountModal').modal('hide');
      this.selectedBank = null;
      this.newAccountNumber = '';
    },
    addFunds() {
      window.axios.post('api/add-money-venezuela', {
        to_account_id: this.selectedAccount.id,
        amount: this.amount,
      }).then(() => {
        this.amount = '';
        this.selectedOperator = null;
        this.selectedAccount = null;
        alert('monto añadido exitosamente');
      });
    },
    getBanks() {
      window.axios.get('api/banks').then((response) => {
        this.banks = response.data;
      });
    },
  },
};
</script>

<style scoped>

</style>
