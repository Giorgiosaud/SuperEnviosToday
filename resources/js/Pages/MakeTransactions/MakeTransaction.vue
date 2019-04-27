<template>
  <div class="container">
    <div class="row client_data">
      <div class="col-12">
        <h1>Transcacción</h1>
      </div>
      <div class="col-12">
        Datos del Cliente Emisor
        <hr>
      </div>
      <div class="col-12 col-md-6">
        <label
          for="idn_type"
          class="label-base"
        >Tipo de Identificación</label>
        <v-select
          id="idn_type"
          v-model="idn_type"
          v-validate="'required'"
          :searchable="false"
          :clearable="false"
          :options="idnTypes"
          name="Tipo de Identificación"
          class="mb-3 input-base p-0"
        />
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Tipo de Identificación') }}</strong>
        </span>
      </div>
      <div class="col-12 col-md-6">
        <label
          for="idn"
          class="label-base"
        >Numero de Identificación</label>

        <input
          id="idn"
          v-model="idn"
          v-validate="'required'"
          type="text"
          class="input-base"
          name="Número de Identificación"
          required
          autofocus
          @blur="buscarCliente"
        >
        <span
          class="error-base"
          role="alert"
        >
          <strong>{{ errors.first('Número de Identificación') }}</strong>
        </span>
      </div>
      <div class="col-12">
        <button
          :disabled="agregarDisabled"
          class="btn btn-primary"
          @click="agregarCliente"
        >
          Agregar Cliente
        </button>
      </div>
    </div>
    <div
      v-if="loadingClientData"
      class="w-100 d-flex align-center justify-content-center"
    >
      <div class="loading">
          <div/>
          <div/>
          <div/>
          <div/>
      </div>
    </div>
    <div
      v-else
      class="row client_details"
    >
      <div class="col-12 col-md-6 col-lg-4">
        <label
          for="name"
          class="label-base"
        >Nombre</label>
        <input
          id="name"
          v-model="client.name"
          :disabled="inputClientDisabled"
          type="text"
          class="input-base"
          name="name"
          required
          autofocus
        >
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <label
          for="last_name"
          class="label-base"
        >Apellido(s)</label>
        <input
          id="last_name"
          v-model="client.last_name"
          :disabled="inputClientDisabled"
          type="text"
          class="input-base"
          name="last_name"
          required
          autofocus
        >
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <label
          for="phone"
          class="label-base"
        >Telefono</label>

        <input
          id="phone"
          v-model="client.phone"
          :disabled="inputClientDisabled"
          type="text"
          class="input-base"
          name="phone"
          required
          autofocus
        >
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <label
          class="label-base"
          for="email"
        >Email</label>
        <input
          id="email"
          v-model="client.email"
          :disabled="inputClientDisabled"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus
        >
      </div>
      <div class="col-12 col-md-6 col-lg-4">
        <label
          for="address"
          class="label-base"
        >Dirección</label>
        <textarea
          id="address"
          v-model="client.address"
          :disabled="inputClientDisabled"
          class="input-base"
          required
          autofocus
          name="address"
        >Dirección</textarea>
      </div>
    </div>
    <div class="row transaction_details">
      <div class="col-12">
        <h2>Datos de la transacción</h2>
        <hr>
      </div>
      <div class="col-12">
        <label
          for="currency"
          class="label-base"
        >Seleccione Moneda:</label>
        <v-select
          id="currency"
          v-model="selectedCurrency"
          :searchable="false"
          :options="currencies"
          :clearable="false"
          label="name"
          class="input-base"
        />
      </div>
      <div class="col-12">
        <label
          for="foreign_account"
          class="label-base"
        >Seleccione Cuenta Donde se recibio el dinero:</label>
        <v-select
          id="foreign_account"
          v-model="selectedOperatorAccount"
          :searchable="false"
          :clearable="false"
          :options="operatorAccounts"
          :disabled="!selectedCurrency"
          class="input-base"
        />
      </div>
      <div class="col-12">
        <label
          for="amount"
          class="label-base"
        >Ingrese un monto</label>
        <input
          id="amount"
          v-model="amount"
          type="text"
          class="input-base"
        >
      </div>
      <div class="col-12">
        <h3>Tasa Actual {{ actualRate | currency }}</h3>
      </div>
      <div class="col-12">
        <label for="anotherRate">¿Desea pedir autorizacion para utilizar otra tasa de cambio?
          <input
            id="anotherRate"
            v-model="anotherRate"
            type="checkbox"
          >
        </label>
      </div>
      <div class="col-12">
        <h3>Monto en Bs {{ actualRate * amount| currency }}</h3>
      </div>
      <div
        v-if="anotherRate"
        class="col-12"
      >
        <div class="col-12">
          <label
            for="rate"
            class="label-base"
          >Ingrese una tasa sugerida</label>
          <input
            id="rate"
            v-model="actualRate"
            type="text"
            class="input-base"
          >
        </div>
      </div>
      <div class="col-12">
        <vue-dropzone
          id="dropzone"
          ref="myVueDropzone"
          v-model="dropImage1"
          :options="dropzoneOptions"
          :duplicate-check="true"
          @vdropzone-success="saveClientVoucher"
          @vdropzone-duplicate-file="alertDuplicateFile"
          @vdropzone-error="errorSaveClientVoucher"
        />
      </div>
    </div>
    <div class="row receivers">
      <div class="col-12">
        <h2>Receptores Registrados para este cliente</h2>
        <hr>
      </div>
      <div
        v-if="client.receivers && client.receivers.length"
        class="col-12"
      >
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th>Idn Type</th>
              <th>Idn</th>
              <th>Nombre</th>
              <th>Apellido</th>
              <th>Accion</th>
            </tr>
            <tr
              v-for="(receiver , receiverIndex) in client.receivers"
              :key="receiverIndex"
              :class="{active:selectedReceiver ===receiver}"
            >
              <td>{{ receiver.idn_type }}</td>
              <td>{{ receiver.idn }}</td>
              <td>{{ receiver.name }}</td>
              <td>{{ receiver.last_name }}</td>
              <td>
                <button
                  class="btn btn-primary"
                  @click="assignReceiver(receiver)"
                >
                  Seleccionar Receptor
                </button>
              </td>
            </tr>
          </table>
        </div>
      </div>
      <div class="col-12">
        <button
          :disabled="!client.id"
          class="btn btn-primary"
          @click="agregarUsuarioReceptor"
        >
          Agregar Receptor
        </button>
      </div>
    </div>
    <div class="row receiver_account">
      <div class="col-12">
        <h2>Seleccione Cuenta o asocie una a este receptor</h2>
        <hr>
      </div>
      <div
        v-if="selectedReceiver.accounts && selectedReceiver.accounts.length"
        class="col-12"
      >
        <div class="col-12">
          <div class="table-responsive">
            <table class="table">
              <tr>
                <th>Tipo de Moneda</th>
                <th>Nombre de Banco</th>
                <th>Numero de Cuenta</th>
                <th>Accion</th>
              </tr>
              <tr
                v-for="account in selectedReceiver.accounts"
                :key="account.id"
                :class="{active:selectedReceiverAccount === account}"
              >
                <td>{{ account.bank.currency.name }}</td>
                <td>{{ account.bank.name }}</td>
                <td>{{ account.number }}</td>
                <td>
                  <button
                    class="btn btn-primary"
                    @click="assignAccount(account)"
                  >
                    Seleccionar Cuenta
                  </button>
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
      <div class="col-12">
        <button
          :disabled="!selectedReceiver.id"
          class="btn btn-primary"
          @click="agregarCuenta"
        >
          Agregar Cuenta
        </button>
      </div>
      <hr>
    </div>
    <div class="row operadores_venezuela">
      <div class="col-12">
        <h2>Operadores Venezuela Disponibles</h2>
        <button
          class="btn btn-primary"
          @click="updateOperatorBalance"
        >
          Actualizar
        </button>
        <hr>
      </div>
      <div
        v-for="(operador, opvenindex) in operadoresVenezuela"
        :key="opvenindex"
        class="col-12"
      >
        {{ operador.name }} {{ operador.last_name }}
        <div class="table-responsive">
          <table class="table">
            <tr>
              <th>
                Banco
              </th>
              <th>
                Cuenta
              </th>
              <th>
                Saldo
              </th>
              <th>
                Accion
              </th>
            </tr>
            <tr
              v-for="(venezuelan_account,vacindex) in operador.accounts"
              :key="vacindex"
              :class="{active:selectedvenezuelanAccount === venezuelan_account.id}"
            >
              <td>{{ venezuelan_account.bank.name }}</td>
              <td>{{ venezuelan_account.number }}</td>
              <td>{{ venezuelan_account.Balance|currency }}</td>
              <td>
                <button
                  class="btn btn-primary"
                  @click="assignVenezuelanAccount(venezuelan_account)"
                >
                  Seleccionar Cuenta Venezuela
                </button>
              </td>
            </tr>
          </table>
        </div>
      </div>
    </div>
    <div class="row final_button_transaction">
      <div class="col-12">
        <button
          :disabled="!selectedReceiver.id"
          class="btn btn-primary"
          @click="agregarTransaccion"
        >
          Agregar Transacción
        </button>
      </div>
    </div>
    <div
      id="modal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modalExtraInfo"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            {{ modalTitle }}
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">×</span>
            </button>
          </div>
          <div class="modal-body">
            <component
              :is="modalComponent"
              v-bind="propsOfComponent"
              @registered="buscarCliente"
              @accountRegistered="cuentaAgregada"
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
    import vue2Dropzone from 'vue2-dropzone';
    import addVenezuelanAccount from '../../components/addVenezuelanAccount';
    import 'vue2-dropzone/dist/vue2Dropzone.min.css';

    export default {
        name: 'MakeTransactions',
  components: {
    addVenezuelanAccount,
    vueDropzone: vue2Dropzone,

  },
  data() {
    return {
      amount: '',
      actualRate: 0,
      inputClientDisabled: true,
      agregarDisabled: true,
      loadingClientData: false,
      operadoresVenezuela: [],
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT', 'RIF'],
      idn_type: '',
      anotherRate: false,
      rate: '',
      idn: '',
      client: {},
      selectedReceiver: {},
      selectedReceiverAccount: {},
      modalTitle: '',
      modalComponent: '',
      selectedvenezuelanAccount: '',
      selectedCurrency: null,
      currencies: [],
      operator: null,
      selectedOperatorAccount: '',
      propsOfComponent: {},
      dropImage1: null,
      clientTransactionAttachmentId: [],
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
      relatedRateInt() {
          return parseInt(this.actualRate.replace(',', '.'), 10);
      },
    operatorAccounts() {
      if (!this.operator || !this.selectedCurrency) {
        return [];
      }
      const accounts = this.operator.accounts
        .filter(acc => acc.bank.currency.id === this.selectedCurrency.id);
      accounts.forEach((acc) => {
        acc.label = `${acc.bank.name} / ${acc.number}`;
      });
      return accounts;
    },
  },
  watch: {
    idn_type() {
      this.buscarCliente();
    },
    selectedReceiver() {
      this.selectedReceiverAccount = null;
    },
    selectedCurrency(currency) {
      this.selectedOperatorAccount = null;
      axios.get(`api/last_rate/${currency.id}`).then((response) => {
          this.actualRate


              = response.data.amount;
      });
    },
  },
  created() {
    axios.get('api/my_info').then(({ data }) => {
      this.operator = data;
    });
    axios.get('api/operadores-venezuela').then((response) => {
      this.operadoresVenezuela = response.data;
    });
    axios.get('api/foreign_currencies').then(({ data }) => {
      this.currencies = data;
    });
  },
  mounted() {
    Echo.private('transaction-assigned')
      .listen('TransactionExecuted', (e) => {
        console.log(e);
        this.updateOperatorBalance();
      });
  },
  methods: {
    updateOperatorBalance() {
      axios.get('api/operadores-venezuela').then((response) => {
        this.operadoresVenezuela = response.data;
      });
    },
    saveClientVoucher(_, file) {
      this.clientTransactionAttachmentId.push(file.id);
    },
    errorSaveClientVoucher(files) {
      this.$refs.myVueDropzone.removeFile(files);
    },
      alertDuplicateFile() {
          alert('archivo duplicado');
      },
      assignVenezuelanAccount(account) {
      this.selectedvenezuelanAccount = account.id;
    },
    agregarCliente() {
      this.modalTitle = 'Agregar Cliente';
      this.modalComponent = 'register-client';
      this.propsOfComponent = {
        idnTypeImported: this.idn_type,
        idnImported: this.idn,
      };
      $('#modal').modal('show');
    },
    assignReceiver(receiver) {
      if (this.selectedReceiver === receiver) {
        this.selectedReceiver = null;
        return;
      }
      this.selectedReceiver = receiver;
    },
    assignAccount(account) {
      if (this.selectedReceiverAccount === account) {
        this.selectedReceiverAccount = null;
        return;
      }
      this.selectedReceiverAccount = account;
    },
    agregarCuenta() {
      this.modalTitle = 'Agregar Cuenta';
      this.modalComponent = 'add-venezuelan-account';
      this.propsOfComponent = {
        client: this.selectedReceiver,
      };
      $('#modal').modal('show');
    },
    agregarUsuarioReceptor() {
      this.modalTitle = 'Agregar Receptor';
      this.modalComponent = 'register-client';
      this.propsOfComponent = {
        clientParent: this.client.id,
      };
      $('#modal').modal('show');
    },
    buscarCliente() {
      $('#modal').modal('hide');
      if (this.idn !== '' && this.idn_type !== '') {
        this.loadingClientData = true;
        return axios.get('api/user_data', {
          params: {
            idn_type: this.idn_type, idn: this.idn,
          },
        })
          .then(({ data }) => {
            if (!data.length) {
              this.agregarDisabled = false;
              this.client = {};
            } else {
              this.agregarDisabled = true;
              [this.client] = data;
            }
          }).finally(() => {
            this.loadingClientData = false;
          });
      }
    },
    cuentaAgregada() {
      const selectedReceiverId = this.selectedReceiver.id;
      this.buscarCliente().then(() => {
        this.selectedReceiver = this.client.receivers
          .find(receiver => receiver.id === selectedReceiverId);
      });
    },
    agregarTransaccion() {
      const payload = {
        client_id: this.client.id,
        foreign_account_id: this.selectedOperatorAccount.id,
        received_transaction_attachment_ids: this.clientTransactionAttachmentId,
        receiver_account_id: this.selectedReceiverAccount.id,
        venezuelan_operator_account_id: this.selectedvenezuelanAccount,
        rate: this.actualRate,
        amount: this.amount,
      };
      axios.post('api/add-transaction', payload)
        .then(() => {
          alert('transaction ok');
          window.location.reload();
        });
    },
  },
};
</script>

<!--suppress CssUnusedSymbol -->
<style scoped>
    tr.active {
        background: #bcdefa;
    }

    .modal-dialog {
        max-width: 90%;
    }
</style>
