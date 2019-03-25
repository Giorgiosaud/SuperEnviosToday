<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>Transcaccion</h1>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        Datos del Cliente Emisor
        <hr>
      </div>
    </div>
    <div class="row">
      <div class="col-6 col-md-4">
        <label
          for="idn_type"
          class="label-base"
        >Tipo de Identificación</label>
        <v-select
          id="idn_type"
          v-model="idn_type"
          :searchable="false"
          :clearable="false"
          :options="idnTypes"
          name="idn_type"
          class="mb-3 input-base p-0"
        />
      </div>
      <div class="col-6 col-md-4">
        <label
          for="idn"
          class="label-base"
        >Numero de Identificación</label>

        <input
          id="idn"
          v-model="idn"
          type="text"
          class="input-base"
          name="idn"
          required
          autofocus
          @blur="buscarCliente"
        >
      </div>
    </div>
    <div class="row">
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
    <div class="row">
      <div class="col-6 col-md-4">
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
      <div class="col-6 col-md-4">
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
      <div class="col-6 col-md-4">
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
      <div class="col-6 col-md-4">
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
      <div class="col-6 col-md-4">
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
    <div class="row">
      <div class="col-12">
        <h2>Receptores Registrados para este cliente</h2>
        <hr>
      </div>
    </div>
    <div
      v-if="client.receivers && client.receivers.length"
      class="row"
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
    <div class="row">
      <div class="col-12">
        <button
          :disabled="!client.id"
          class="btn btn-primary"
          @click="agregarCliente"
        >
          Agregar Receptor
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h2>Seleccione Cuenta o asocie una a este receptor</h2>
        <hr>
      </div>
    </div>
    <div
      v-if="selectedReceiver.accounts && selectedReceiver.accounts.length"
      class="row"
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
    <div class="row">
      <div class="col-12">
        <button
          :disabled="!selectedReceiver.id"
          class="btn btn-primary"
          @click="agregarCuenta"
        >
          Agregar Cuenta
        </button>
      </div>
    </div>
    <hr>

    <div class="row">
      <div class="col-12">
        <h2>Datos de la transacción</h2>
        <hr>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <label
          for="currency"
          class="label-base bg-white"
        >Seleccione Moneda:</label>
        <v-select
          id="currency"
          v-model="selectedCurrency"
          :searchable="false"
          :options="currencies"
          label="name"
          class="input-base"
        />
      </div>
      <div class="col-12">
        <label
          for="foreign_account"
          class="label-base bg-white"
        >Seleccione Cuenta Receptor:</label>
        <v-select
          id="foreign_account"
          v-model="selectedOperatorAccount"
          :searchable="false"
          :options="operatorAccounts"
          class="input-base"
        />
      </div>
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
    <div class="row">
      <div class="col-12">
        <h3>Tasa Actual {{ actualRate | currency }}</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h3>Monto en Bs {{ actualRate * amount| currency }}</h3>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <h2>Operadores Venezuela Disponibles</h2>
        <hr>
      </div>
    </div>
    <div class="row">
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
              <td>{{ venezuelan_account.TotalAmount|currency }}</td>
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
    <div class="row">
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
            />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Transactions',
  data() {
    return {
      amount: '',
      actualRate: 0,
      inputClientDisabled: true,
      agregarDisabled: true,
      operadoresVenezuela: [],
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT','RIF'],
      idn_type: '',
      idn: '',
      client: {},
      selectedReceiver: {},
      selectedReceiverAccount: {},
      modalTitle: '',
      modalComponent: '',
      selectedvenezuelanAccount: '',
      selectedCurrency: '',
      currencies: [],
      operator: null,
      selectedOperatorAccount: '',
    };
  },
  computed: {
    operatorAccounts() {
      if (!this.operator) {
        return [];
      }
      const accounts = this.operator.accounts
        .filter(acc => acc.bank.currency.id === this.selectedCurrency.id);
      accounts.forEach((acc) => { acc.label = acc.bank.name; });
      return accounts;
    },
    propsOfComponent() {
      const props = {};
      if (this.modalComponent === 'register-client') {
        if (this.client.id) {
          props.clientParent = this.client.id;
        } else {
          props.idn_imported = this.idn;
          props.idn_type_imported = this.idn_type;
        }
      } else {
        props.client = this.selectedReceiver;
      }
      return props;
    },
  },
  watch: {
    idn_type() {
      this.buscarCliente();
    },
    selectedCurrency(val) {
      this.selectedOperatorAccount = null;
      axios.get(`api/last_rate/${val.id}`).then((response) => {
        this.actualRate = response.data.amount;
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
  methods: {
    assignVenezuelanAccount(account) {
      this.selectedvenezuelanAccount = account.id;
    },
    agregarCliente() {
      this.modalTitle = 'Agregar Cliente';
      this.modalComponent = 'register-client';
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
      this.modalComponent = 'add-account';
      $('#modal').modal('show');
    },
    agregarUsuarioReceptor() {
      this.modalTitle = 'Agregar Receptor';
      this.modalComponent = 'register-client';
      $('#modal').modal('show');
    },
    buscarCliente() {
      $('#modal').modal('hide');
      if (this.idn !== '' && this.idn_type !== '') {
        axios.get('api/user_data', {
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
          });
      }
    },
    agregarTransaccion() {
      const payload = {
        to_account_id: this.selectedReceiverAccount.id,
        operator_account_id: this.selectedOperatorAccount.id,
        from_account_id: this.selectedvenezuelanAccount,
        from_client_id: this.client.id,
        foreign_currency_id: this.selectedCurrency.id,
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
tr.active{
    background:#bcdefa;
}
.modal-dialog{
    max-width: 90%;
}
</style>
