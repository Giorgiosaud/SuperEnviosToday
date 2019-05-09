<template>
  <div>
    <div class="container">
      <div class="row">
        <div class="col-12">
          <h1>
            Mis Transacciones({{ myTransactionsCount }})
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
                  v-for="(transaction, transactionKey) in venezuelanTransactions"
                  :key="transactionKey"
                  :class="{'bg-success':transaction.status==='executed'||
                    transaction.status==='completed'}"
                >
                  <td
                    v-for="(key, keyIndex) in keysToShow"
                    :key="keyIndex"
                  >
                    <span v-if="key==='receiverIdn'">
                      {{ transaction.destination_account.owner.idn_type }} -
                      {{ transaction.destination_account.owner.idn }}
                    </span>
                    <span v-if="key==='receiverName'">
                      {{ transaction.destination_account.owner.name }}
                      {{ transaction.destination_account.owner.last_name }}
                    </span>
                    <span v-if="key==='foreign_operator'">
                      {{ transaction.parent_transaction.destination_account.owner.name }}
                      {{ transaction.parent_transaction.destination_account.owner.last_name }}
                    </span>
                    <span v-if="key==='operator_venezuela_bank'">
                      {{ transaction.origin_account.bank.name }}
                    </span>
                    <span v-if="key==='receiver_bank'">
                      {{ transaction.destination_account.bank.name }}
                    </span>
                    <span v-if="key==='amount'">
                      {{ transaction.amount|currency }}
                    </span>
                    <span
                      v-if="key==='action' && transaction.status!=='executed'&&
                        transaction.status!=='completed'"
                      @click="selectTransaction(transaction)"
                    >
                      <button class="btn btn-primary">Ver mas y completar</button>
                    </span>
                    <span v-else-if="key==='action' && transaction.status==='executed'">
                      Ejecutada
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
                <h2>Detalles del receptor</h2>
              </div>
              <div class="row">
                <div
                  v-if="selectedTransaction"
                  class="col-12 col-md-6"
                >
                  <div class="row">
                    Identificacion: {{ selectedTransaction.destination_account.owner.idn_type }} -
                    {{ selectedTransaction.destination_account.owner.idn }}
                  </div>

                  <div class="row">
                    Nombre: {{ selectedTransaction.destination_account.owner.name }}
                  </div>
                  <div class="row">
                    Apellido(s): {{ selectedTransaction.destination_account.owner.last_name }}
                  </div>
                  <div class="row">
                    Telefono: {{ selectedTransaction.destination_account.owner.phone }}
                  </div>
                  <div class="row">
                    Email:{{ selectedTransaction.destination_account.owner.email }}
                  </div>
                  <div class="row">
                    Direccion:{{ selectedTransaction.destination_account.owner.address }}
                  </div>
                </div>
                <div class="col-12 col-md-6">
                  <div class="col-12">
                    Banco: {{ selectedTransaction.destination_account.bank.name }}
                  </div>
                  <div class="col-12">
                    Tipo: {{ selectedTransaction.destination_account.type }}
                  </div>
                  <div class="col-12">
                    Número: {{ selectedTransaction.destination_account.number }}
                  </div>
                  <div class="col-12">
                    Monto: {{ selectedTransaction.amount | currency }}
                  </div>
                </div>
              </div>
              <div class="row">
                <h2>Detalles de la Transferencia</h2>
              </div>
              <div class="row">
                <label
                  class="label-base"
                  for="numeroDeTransaccion"
                >Numero de Transaccion</label>
                <input
                  id="numeroDeTransaccion"
                  v-model="transactionNumber"
                  class="input-base"
                  type="text"
                >
              </div>
              <div class="row">
                <div class="col-12">
                  <vue-dropzone
                    id="dropzone"
                    ref="myVueDropzone"
                    v-model="dropImage1"
                    :duplicate-check="true"
                    :options="dropzoneOptions"
                    @vdropzone-duplicate-file="alertDuplicateFile"
                    @vdropzone-error="errorSaveClientVoucher"
                    @vdropzone-success="saveClientVoucher"
                  />
                </div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button
              :disabled="transactionNumber.length===0 || transactionAttachments.length===0"
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
import vue2Dropzone from 'vue2-dropzone';
import 'vue2-dropzone/dist/vue2Dropzone.min.css';

export default {
  name: 'VenezuelanTransactions',
  components: {
    vueDropzone: vue2Dropzone,
  },
  data() {
    return {
      venezuelanTransactions: [],
      loading: true,
      empty: false,
      dropImage1: null,
      selectedTransaction: null,
      transactionNumber: '',
      myTransactionsCount: 0,
      transactionAttachments: [],
      headers: [
        'Identificación Receptor', 'Nombre Receptor', 'Nombre Operador Foraneo', 'Banco Operador Venezuela', 'Banco Receptor', 'Monto', 'Acción',
      ],
      keysToShow: [
        'receiverIdn', 'receiverName', 'foreign_operator', 'operator_venezuela_bank', 'receiver_bank', 'amount', 'action',
      ],
    };
  },
  computed: {
    dropzoneOptions() {
      return {
        url: 'api/attachment',
        thumbnailWidth: 150,
        maxFilesize: 3,
        acceptedFiles: 'image/*,application/pdf',
        uploadMultiple: false,
        maxFiles: 3,
        dictDefaultMessage: 'Agregue archivo aqui',
        dictFallbackMessage: 'Este explorador no soporta este uploader',
        dictFileTooBig: 'Archivo muy pesado',
        dictInvalidFileType: 'tipo de archivo invalido',
        dictCancelUpload: 'Upload Cancelado',
        dictRemoveFile: 'Archivo Borrado',
      };
    },
  },
  mounted() {
    $('#modal').on('hidden.bs.modal', () => {
      this.selectedTransaction = null;
      this.transactionNumber = '';
      this.transactionAttachments = [];
    });
  },
  created() {
    this.getVenezuelanTransactions();
    this.getTransactionsCount();
  },
  methods: {
    getTransactionsCount() {
      axios.get('/api/my-transactions-count').then((response) => {
        this.myTransactionsCount = response.data;
      });
    },
    confirmarTransferencia() {
      axios.patch(`api/transaction/${this.selectedTransaction.id}`, {
        attachments: this.transactionAttachments,
        transactionNumber: this.transactionNumber,
      }).then(() => {
        this.myTransactionsCount = this.myTransactionsCount - 1;
        this.getVenezuelanTransactions();
        $('#modal').modal('hide');
      })
        .finally(() => {
          this.selectedTransaction = null;
          this.transactionNumber = '';
          this.transactionAttachments = [];
        });
    },
    selectTransaction(transaction) {
      this.selectedTransaction = transaction;
      axios.patch(`api/transaction/in-progress/${transaction.id}`);
      $('#modal').modal('show');
    },
    saveClientVoucher(_, file) {
      this.transactionAttachments.push(file.id);
    },
    alertDuplicateFile() {
      alert('archivo duplicado');
    },
    errorSaveClientVoucher(files) {
      this.$refs.myVueDropzone.removeFile(files);
    },
    getVenezuelanTransactions() {
      this.loading = true;
      axios.get('api/my-venezuelan-transactions').then((response) => {
        this.venezuelanTransactions = response.data.data;
        this.empty = response.data.data === 0;
      }).finally(() => {
        this.loading = false;
      });
    },
  },
  // TODO make show list of transactions and modal to accomplish transactions
};
</script>
<style scoped>

</style>
