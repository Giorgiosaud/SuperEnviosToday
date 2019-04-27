<template>
  <div>
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h1>
            Mis Transacciones Relacionadas
          </h1>
        </div>
      </div>
      <div class="row">
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
                  v-for="(transaction, transactionKey) in myTransactions"
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
                    <span v-else-if="key==='operator_destination'">
                      {{ transaction.destination_account.owner.name }}
                      {{ transaction.destination_account.owner.last_name }}
                    </span>
                    <span v-else-if="key==='bank_destination'">
                      {{ transaction.destination_account.bank.name }} /
                      {{ transaction.destination_account.bank.currency.name }}
                    </span>
                      <span v-else-if="key==='status' && transaction.status==='confirmed'">
                      Confirmada
                    </span>
                      <span v-else-if="key==='status' && transaction.status==='assigned'">
                      Asignada
                    </span>
                      <span v-else-if="key==='status' && transaction.status==='terminated'">
                      Terminada
                    </span>
                      <span v-else-if="key==='status' && transaction.status==='in_progress'">
                      En Progreso
                    </span>
                      <span v-else-if="key==='status' && transaction.status==='executed'">
                      Ejecutada
                    </span>
                      <span v-else-if="key==='created_at'">
                      {{ transaction[key] }}
                    </span>
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
    </div>
  </div>
</template>

<script>
export default {
  name: 'ListTransactions',
  data() {
    return {
      myTransactions: [],
        myTransactionsCount: 0,
      loading: true,
      headers: [
          'Identificación cliente', 'Nombre Cliente', 'Nombre Destino', 'Banco Destino', 'Monto', 'Estado', 'Fecha de Apertura',
      ],
      keysToShow: [
          'idn', 'name', 'operator_destination', 'bank_destination', 'amount', 'status', 'created_at',
      ],
    };
  },
  created() {
    this.getTransactions();
  },
  methods: {
    getTransactions() {
      this.loading = true;
      axios.get('/api/my-transactions').then((response) => {
        this.myTransactions = response.data.data;
        this.loading = false;
        this.empty = this.myTransactions.length === 0;
      });
    },
  },
};
</script>

<style scoped>

</style>
