<template>
  <div class="container">
    <div class="row">
      <h1>Asociar Cuentas a Operadores</h1>
    </div>
    <div class="row">
      <div class="col-12">
        <label for="currencyId">Seleccione el Operador</label>

        <v-select
          v-model="selectedOperator"
          v-validate="'required'"
          :options="users"
          :clearable="false"
          label="name"
          name="Usuario"
        >
          <template
            slot="option"
            slot-scope="option"
          >
            {{ option.name }} {{ option.last_name }}
          </template>
        </v-select>
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
        <label for="accountNumber">Seleccione La Cuenta</label>
        <v-select
          id="accountId"
          v-model="selectedAccount"
          :options="accountsFiltered"
          :clearable="false"
          label="number"
          name="Cuenta"
        />
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Cuenta') }}</strong>
        </span>
      </div>
      <div class="col-12">
        <button
          type="button"
          class="btn btn-primary mt-2"
          @click.prevent="addAccount"
        >
          Asociar Cuenta
        </button>
      </div>
      <div
        v-if="selectedOperator && selectedOperator.accounts.length"
        class="col-12 py-2"
      >
        <div class="table-responsive">
          <table>
            <legend>Cuentas asociadas de este cliente</legend>
            <tr>
              <th>Número</th>
              <th>Banco</th>
              <th>Moneda</th>
              <th>Accion</th>
            </tr>
            <tr
              v-for="account in selectedOperator.accounts"
              :key="account.id"
            >
              <td>{{ account.number }}</td>
              <td>{{ account.bank.name }}</td>
              <td>{{ account.bank.currency.name }}</td>
              <td>
                <button
                  class="btn btn-danger"
                  @click="unasociateAccount(account)"
                >
                  Desasociar
                </button>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div
        v-else
        class="py-2"
      >
        <h4 v-if="!selectedOperator">
          Seleccione un operador
        </h4>
        <h4 v-else>
          Este operador no tiene cuentas asociadas
        </h4>
      </div>
    </div>
  </div>
</template>

<script>
/* eslint-disable no-alert */

import axios from 'axios';

export default {
  name: 'AsociateAccountToOperator',
  data() {
    return {
      selectedOperator: null,
      users: [],
      selectedCurrency: null,
      selectedBank: null,
      selectedAccount: null,
      accounts: [],
    };
  },
  computed: {
    accountsFiltered() {
      if (this.selectedBank) { return this.accounts.filter(acc => acc.bank.id === this.selectedBank.id); }
      return [];
    },
    banks() {
      if (this.selectedCurrency) {
        return [...new Set(this.accounts.map(acc => acc.bank)
          .filter(bank => bank.currency_id === this.selectedCurrency.id)
          .map(bank => bank.id))]
          .map(id => this.accounts.find(acc => acc.bank.id === id).bank);
      }
      return [];
    },
    currencies() {
      return [...new Set(this.accounts.map(acc => acc.bank.currency.id))]
        .map(id => this.accounts
          .find(acc => acc.bank.currency.id === id).bank.currency);
    },
  },
  created() {
    this.getForgeinUsers();
    this.getForgeignAccounts();
  },
  methods: {
    getForgeignAccounts() {
      axios.get('api/operators-accounts').then((response) => {
        this.accounts = response.data;
      });
    },
    addAccount() {
      axios.post('api/operator-asociate-account', {
        user_id: this.selectedOperator.id,
        account_id: this.selectedAccount.id,
      })
        .then(() => {
          alert('cuenta añadida exitosamente');
          window.location.reload();
        });
    },
    unasociateAccount(account) {
      axios.delete(`api/operator-desasociate-account/${account.id}/${this.selectedOperator.id}`)
        .then(() => {
          alert('cuenta desuscrita exitosamente');
          window.location.reload();
        });
    },
    getForgeinUsers() {
      axios.get('api/operators').then(({ data }) => {
        this.users = data;
      });
    },
  },
};
</script>

<style scoped>
</style>
