<template>
  <div>
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h1>
            Transacciones Pendientes
          </h1>
        </div>
        <div class="col-12 col-md-9">
          <label
            for="query"
            class="label-base"
          >Consulta</label> <input
            id="query"
            v-model="query"
            class="input-base"
          >
        </div>
      </div>
    </div>
    <div
      v-if="loading"
      class="w-100 d-flex align-center justify-content-center"
    >
      <div class="loading">
        <div /><div /><div /><div />
      </div>
    </div>
    <div
      v-else-if="empty"
      class="w-100 d-flex align-center justify-content-center"
    >
      <h2>no hay operaciones pendientes</h2>
    </div>
    <div
      v-else
      class="container"
    >
      <div
        class="table-responsive"
      >
        <table class="table">
          <thead>
            <tr>
              <th
                v-for="(header, headerIndex) in headers"
                :key="headerIndex"
                class="text-left"
              >
                {{ header }}
              </th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="(transaction, transactionKey) in transactions"
              :key="transactionKey"
            >
              <td
                v-for="(key, keyIndex) in keysToShow"
                :key="keyIndex"
              >
                <span v-if="key==='idn'">
                  {{ transaction.client.idn_type }} - {{ transaction.client.idn }}
                </span>
                <span v-else-if="key==='name'">
                  {{ transaction.client.name }} {{ transaction.client.last_name }}
                </span>
                <span v-else-if="key==='foreign_operator'">
                  {{ transaction.foreign_operator.name }} {{ transaction.foreign_operator.last_name }}
                </span>
                <span v-else-if="key==='receiver_bank'">
                  {{ foreignAccount(transaction).bank.name }} / {{ foreignAccount(transaction).number }} - {{ transaction.transaction_number }}
                </span>
                <span v-else-if="key==='operator_venezuela'">
                  {{ transaction.venezuelan_operator.name }} {{ transaction.venezuelan_operator.last_name }}
                </span>

                <span v-else-if="key==='operator_bank'">
                  {{ transaction.operator_account.bank.name }}
                </span>
                <span v-else-if="key==='calculated_amount'">
                  {{ transaction.amount * transaction.rate |currency }}
                </span>
                <div
                  v-else-if="key==='action'"
                  class="row"
                >
                  <div v-if="transaction.status==='pending'">
                    <button
                      class="btn btn-primary"
                      :disabled="onChangeState"
                      @click="approveTransation(transaction)"
                    >
                      Aprobar
                    </button>
                    <button
                      class="btn btn-danger"
                      :disabled="onChangeState"
                      @click="rejectTransation(transaction)"
                    >
                      Rechazar
                    </button>
                  </div>
                  <span v-else-if="transaction.status==='aprooved'">
                    Aprobada
                  </span>
                  <span v-else>
                    Rechazada
                  </span>
                </div>

                <span v-else>
                  {{ transaction[key] | currency }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script>
import { debounce } from 'lodash';

export default {
  name: 'PendingTransactions',
  data() {
    return {
      query: '',
      loading: true,
      empty: false,
      transactions: [],
      onChangeState: false,
      headers: [
        'Identificación cliente', 'Nombre Cliente', 'Operador Extranjero', 'Operador Venezuela', 'Banco Operador Venezuela', 'Banco Receptor / cuenta - numero de transacción', 'Tasa Sugerida', 'Monto', 'Monto Calculado', 'Acción',
      ],
      keysToShow: [
        'idn', 'name', 'foreign_operator', 'operator_venezuela', 'operator_bank', 'receiver_bank', 'rate', 'amount', 'calculated_amount', 'action',
      ],
      pendingTransactions: [],
    };
  },
  created() {
    this.getPendingTransactions();
  },
  watch: {
    query: debounce(function getUsers() {
      if (this.query.length > 0) {
        this.loading = true;
        axios.get('/api/pending-transactions', {
          params: {
            q: this.query,
          },

        }).then((response) => {
          this.setData(response.data);
          this.loading = false;
          this.empty = response.data.data.length === 0;
        });
      } else {
        this.getPendingTransactions();
      }
    }, 400),
  },
  mounted() {
    Echo.private('pending-transaction')
      .listen('PendingTransactionAwaiting', (e) => {
        console.log(e);
        this.getPendingTransactions();
      });
  },
  methods: {
    getPendingTransactions() {
      this.loading = true;
      this.onChangeState = false;
      return axios.get('/api/pending-transactions')
        .then((response) => {
          this.empty = response.data.data.length === 0;
          this.setData(response.data);
          this.loading = false;
        }).finally(() => {
          this.onChangeState = false;
        });
    },
    foreignAccount(transaction) {
      return transaction.foreign_operator.accounts.find(acc => acc.id === transaction.foreign_account_id);
    },
    setData(data) {
      this.transactions = data.data;
      this.last_page = data.last_page;
      this.current_page = data.current_page;
      this.response = data;
    },
    approveTransation(transaction) {
      this.onChangeState = true;
      axios.patch(`api/approve-transaction/${transaction.id}`).then(() => {
        alert('ok');
        this.getPendingTransactions();
      }).finally(() => {
        this.onChangeState = false;
      });
    },
    rejectTransation(transaction) {
      this.onChangeState = true;
      axios.patch(`api/reject-transaction/${transaction.id}`).then(() => {
        alert('ok');
        this.getPendingTransactions();
      }).finally(() => {
        this.onChangeState = false;
      });
    },
  },
};
</script>

<style scoped>
</style>
