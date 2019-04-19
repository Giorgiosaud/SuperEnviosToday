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
                <span v-else-if="key==='receiver_bank'">
                  {{ transaction.receiver_account.bank.name }}
                </span>
                <span v-else-if="key==='operator_venezuela'">
                  {{ transaction.operator_account.owner.name }}
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
                  <button class="btn btn-primary">
                    Aprobar
                  </button>
                  <button class="btn btn-danger">
                    Rechazar
                  </button>
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
export default {
  name: 'PendingTransactions',
  data() {
    return {
      query: '',
      loading: true,
      empty: false,
      transactions: [],
      headers: [
        'Identificacion cliente', 'Nombre Cliente', 'Banco Receptor', 'Operador Venezuela', 'Banco Operador Venezuela', 'Tasa Sugerida', 'Monto', 'Monto Calculado', 'Accion',
      ],
      keysToShow: [
        'idn', 'name', 'receiver_bank', 'operator_venezuela', 'operator_bank', 'rate', 'amount', 'calculated_amount', 'action',
      ],
      pendingTransactions: [],
    };
  },
  created() {
    this.getPendingTransactions();
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
      return axios.get('/api/my-pending-transactions')
        .then((response) => {
          this.empty = response.data.data.length === 0;
          this.setData(response.data);
          this.loading = false;
        });
    },
    setData(data) {
      this.transactions = data.data;
      this.last_page = data.last_page;
      this.current_page = data.current_page;
      this.response = data;
    },
  },
};
</script>

<style scoped>

</style>
