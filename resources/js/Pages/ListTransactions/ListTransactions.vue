<template>
  <div>
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h1>
            Listado de Transacciones
          </h1>
        </div>
      </div>
      <div class="row">
        <div
          v-if="loading"
          class="w-100 d-flex align-center justify-content-center"
        >
          <div class="loading">
            <div />
            <div />
            <div />
            <div />
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
                    <span v-if="key==='operator'">
                      {{ operatorOfTransaction(transaction) }}
                    </span>
                    <span v-if="key==='idn' && transaction.client">
                      {{ transaction.client.idn_type }} - {{ transaction.client.idn }}
                    </span>
                    <span v-else-if="key==='name' && transaction.client">
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
                    <span v-else-if="key==='amount'">
                      {{ transaction[key]|currency }}
                    </span>
                    <span v-else-if="key==='action'">
                      <button
                        class="btn btn-primary"
                        @click="seeTransaction(transaction)"
                      >Ver Transacción</button>
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div
      id="modal"
      aria-hidden="true"
      aria-labelledby="modalExtraInfo"
      class="modal fade"
      role="dialog"
      tabindex="-1"
    >
      <div
        class="modal-dialog modal-xl"
      >
        <div
          v-if="selectedTransaction"
          class="modal-content"
        >
          <div class="modal-header">
            Ver detalles y completar Transaccion #{{ selectedTransaction.id }}
            <button
              aria-label="Close"
              class="close"
              data-dismiss="modal"
              type="button"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <div class="container">
              <div class="row">
                <div class="col-12">
                  <h2>Detalles de la transacción</h2>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <h3>Transaccion Inicial</h3>
                  <hr>
                  Nombre de cliente: {{ selectedTransaction.client.name }}
                  {{ selectedTransaction.client.last_name }}
                  <br>
                  Identificacion: {{ selectedTransaction.client.idn_type }} -
                  {{ selectedTransaction.client.idn }}
                  <br>
                  Monto: {{ selectedTransaction.amount|currency }} {{
                    selectedTransaction.destination_account.bank.currency.identificator }}
                  <br>
                </div>
                <div
                  v-if="selectedTransaction.attachments.length"
                  class="col-12"
                >
                  <h4>Imagenes Relacionadas</h4>
                  <img
                    v-for="(attachment, index) in selectedTransaction.attachments"
                    :key="index"
                    :src="attachment.path"
                    alt="attch"
                  >
                </div>
              </div>
              <div class="row">
                <div
                  v-if="venezuelanTransaction"
                  class="col-12"
                >
                  <h3>Transaccion Venezuela</h3>
                  Nombre del Receptor: {{ venezuelanTransaction.destination_account.owner.name }}
                  {{ venezuelanTransaction.destination_account.owner.last_name }}
                  <br>
                  Identificacion: {{ venezuelanTransaction.destination_account.owner.idn_type }} -
                  {{ venezuelanTransaction.destination_account.owner.idn }}
                  <br>
                  Monto: {{ venezuelanTransaction.amount|currency }} {{
                    venezuelanTransaction.destination_account.bank.currency.identificator }}
                  <br>
                  Impuesto Bancario: {{ venezuelanTax.amount|currency }} {{
                    venezuelanTransaction.destination_account.bank.currency.identificator }}
                  <br>
                  Tasa de cambio {{ venezuelanTransaction.amount/selectedTransaction.amount|currency }}
                  <hr>
                  <div
                    v-if="venezuelanTransaction.attachments.length"
                    class="col-12"
                  >
                    <h4>Imagenes Relacionadas</h4>
                    <a
                      v-for="(attachment, idn2) in venezuelanTransaction.attachments"
                      :key="idn2"
                      :href="attachment.path"
                      target="_blank"
                    >
                      <img
                        :alt="attachment.name"
                        :src="attachment.path"
                        class="img-fluid"
                      >
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer justify-content-between">
            <button
              class="btn btn-link"
              @click="cancelarTransferencia"
            >
              Cerrar
            </button>
            <button
              :disabled="selectedTransaction.status==='terminated'"
              class="btn btn-primary"
              @click="confirmarTransferencia"
            >
              Confirmar
            </button>
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
      selectedTransaction: null,
      myTransactionsCount: 0,
      loading: true,
      headers: [
        'Identificacion operador', 'Identificación cliente', 'Nombre Cliente', 'Nombre Destino', 'Banco Destino', 'Monto', 'Estado', 'Fecha de Apertura', 'Ver Transacción',
      ],
      keysToShow: [
        'operator', 'idn', 'name', 'operator_destination', 'bank_destination', 'amount', 'status', 'created_at', 'action',
      ],
    };
  },
  computed: {

    venezuelanTransaction() {
      if (this.selectedTransaction) {
        return this.selectedTransaction.related_transactions
          .find(transaction => transaction.from_account_id !== null);
      }
      return null;
    },
    venezuelanTax() {
      if (this.selectedTransaction) {
        return this.selectedTransaction.related_transactions
          .find(transaction => transaction.from_account_id === null);
      }
      return null;
    },
  },
  created() {
    this.getTransactions();
  },
  methods: {
    operatorOfTransaction(transaction) {
      return transaction.destination_account.owner.name+ ' '+transaction.destination_account.owner.last_name;
    },
    seeTransaction(transaction) {
      // TODO SEE TRANSACTION
      this.selectedTransaction = transaction;
      $('#modal').modal('show');
    },
    cancelarTransferencia() {
      $('#modal').modal('hide');
    },
    confirmarTransferencia() {
      axios.patch(`/api/finish-transaction/${this.selectedTransaction.id}`)
        .then(() => {
          $('#modal').modal('hide');
        });
    },
    getTransactions() {
      this.loading = true;
      axios.get('/api/all-transactions').then((response) => {
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
