<template>
  <div class="container">
    <div class="row">
      <div class="col-12">
        <h1>Transcacciones</h1>
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
          class="label-base">Tipo de Identificación</label>
        <v-select
          id="idn_type"
          :searchable="false"
          :clearable="false"
          :options="idnTypes"
          v-model="idn_type"
          name="idn_type"
          class="mb-3 input-base p-0"/>
      </div>
      <div class="col-6 col-md-4">
        <label
          for="idn"
          class="label-base">Numero de Identificación</label>

        <input
          id="idn"
          v-model="idn"
          type="text"
          class="input-base"
          name="idn"
          required
          autofocus
          @blur="buscarCliente">
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <button
          :disabled="agregarDisabled"
          class="btn btn-primary"
          @click="agregarCliente">Agregar Cliente</button>
      </div>
    </div>
    <div class="row">
      <div class="col-6 col-md-4">
        <label
          for="name"
          class="label-base">Nombre</label>
        <input
          id="name"
          :disabled="inputClientDisabled"
          v-model="client.name"
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
          class="label-base">Apellido(s)</label>
        <input
          id="last_name"
          v-model="client.last_name"
          :disabled="inputClientDisabled"
          type="text"
          class="input-base"
          name="last_name"
          required
          autofocus>
      </div>
      <div class="col-6 col-md-4">
        <label
          for="phone"
          class="label-base">Telefono</label>

        <input
          id="phone"
          :disabled="inputClientDisabled"
          v-model="client.phone"
          type="text"
          class="input-base"
          name="phone"
          required
          autofocus>
      </div>
      <div class="col-6 col-md-4">
        <label
          class="label-base"
          for="email">Email</label>
        <input
          id="email"
          v-model="client.email"
          :disabled="inputClientDisabled"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
      </div>
      <div class="col-6 col-md-4">
        <label
          for="address"
          class="label-base">Dirección</label>
        <textarea
          id="address"
          v-model="client.address"
          :disabled="inputClientDisabled"
          class="input-base"
          required
          autofocus
          name="address">Dirección</textarea>

      </div>
    </div>
    <div class="row">
      <div class="col-12">
        Receptores Registrados para este cliente
        <hr>
      </div>
    </div>
    <div
      v-if="client.receivers && client.receivers.length"
      class="row">
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
            v-for="receiver in client.receivers"
            :class="{active:selectedReceiver===receiver}">
            <td>{{ receiver.idn_type }}</td>
            <td>{{ receiver.idn }}</td>
            <td>{{ receiver.name }}</td>
            <td>{{ receiver.last_name }}</td>
            <td><button
              class="btn btn-primary"
              @click="assignReceiver(receiver)">Seleccionar Receptor</button></td>
          </tr>
        </table>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        Datos de la tr
        <hr>
      </div>
    </div>
    <div class="row">
      <div class="col-12">
        <button
          :disabled="!client.id"
          class="btn btn-primary"
          @click="agregarUsuarioReceptor">Agregar Receptor</button>
      </div>
    </div>
    <hr>
    <div
      id="modal"
      class="modal fade"
      tabindex="-1"
      role="dialog"
      aria-labelledby="modalExtraInfo"
      aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            {{ modalTitle }}
          </div>
          <div class="modal-body">
            <register-client
              v-bind="propsOfComponent"
              @registered="buscarCliente"/>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!--div class="col-12 col-md-6">
    Lista de Operadores y Cuentas
    <div
      v-for="operador in operadoresVenezuela"
      :key="operador.id">
      {{ operador.name }} {{ operador.last_name }}
      <div
        class="table-responsive">
        <table class="table">
          <tr>
            <th>Nombre de Banco</th>
            <th>Numero de cuenta</th>
            <th>Monto Total</th>
          </tr>
          <tr
            v-for="account in operador.accounts"
            :key="account.id">
            <th>{{ account.bank.name }}</th>
            <th>{{ account.number }}</th>
            <th>{{ account.TotalAmount }}</th>
          </tr>
        </table>
      </div-->
</template>
<script>
export default {
  name: 'Transactions',
  data() {
    return {
      inputClientDisabled: true,
      agregarDisabled: true,
      operadoresVenezuela: [],
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT'],
      idn_type: '',
      idn: '',
      client: {},
      receiver: {},
      modalTitle: '',
      modalComponent: '',
    };
  },
  computed: {
    propsOfComponent() {
      const props = {};
      if (this.client.id) {
        props.clientParent = this.client.id;
      } else {
        props.idn_imported = this.idn;
        props.idn_type_imported = this.idn_type;
      }
      return props;
    },
  },
  watch: {
    idn_type() {
      this.buscarCliente();
    },
  },
  created() {
    window.axios.get('api/operadores-venezuela').then((response) => {
      this.operadoresVenezuela = response.data;
    });
  },
  methods: {
    agregarCliente() {
      this.modalTitle = 'Agregar Cliente';
      this.modalComponent = 'register-client';
      $('#modal').modal('show');
    },
    assignReceiver(receiver) {
      this.receiver = receiver;
    },
    agregarUsuarioReceptor() {
      this.modalTitle = 'Agregar Receptor';
      this.modalComponent = 'register-client';
      $('#modal').modal('show');
    },
    buscarCliente() {
      console.log('buscando');
      if (this.idn !== '' && this.idn_type !== '') {
        axios.get('api/user_data', {
          params: {
            idn_type: this.idn_type, idn: this.idn,
          },
        })
          .then((response) => {
            if (!response.data.length) {
              this.agregarDisabled = false;
              this.client = {};
            } else {
              this.agregarDisabled = true;
              this.client = response.data[0];
            }
          });
      }
    },
  },
};
</script>

<style scoped>
tr.active{
    background:green;
}
</style>
