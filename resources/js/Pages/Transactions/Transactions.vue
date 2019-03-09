<template>
  <div class="container">
    <div class="row">
      <h1>Transactions</h1>
    </div>
    <div class="row">

      <div class="col-12 col-md-6">
        Datos del Cliente
        <hr>
        <label
          for="idn_type"
          class="label-base">Tipo de Identificación</label>
        <v-select
          id="idn_type"
          :searchable="false"
          :clearable="false"
          :options="idnTypes"
          v-model="client.idn_type"
          name="idn_type"
          class="mb-3"/>
        <label
          for="idn"
          class="label-base">Numero de Identificación</label>

        <input
          id="idn"
          v-model="client.idn"
          type="text"
          class="input-base"
          name="idn"
          required
          autofocus>
        <label
          for="name"
          class="label-base">Nombre</label>

        <input
          id="name"
          v-model="client.name"
          type="text"
          class="input-base"
          name="name"
          required
          autofocus
        >

        <label
          for="last_name"
          class="label-base">Apellido(s)</label>

        <input
          id="last_name"
          v-model="client.last_name"
          type="text"
          class="input-base"
          name="last_name"
          required
          autofocus>
        <label
          for="phone"
          class="label-base">Telefono</label>

        <input
          id="phone"
          v-model="client.phone"
          type="text"
          class="input-base"
          name="phone"
          required
          autofocus>
        <label
          class="label-base"
          for="email">Email</label>
        <input
          id="email"
          v-model="client.email"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
        <label
          class="label-base"
          for="email_confirmation">Confirmacion de Email</label>
        <input
          id="email_confirmation"
          v-model="client.email_confirmation"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
        <label
          for="address"
          class="label-base">Dirección</label>
        <textarea
          id="address"
          v-model="client.address"
          class="input-base"
          required
          autofocus
          name="address"/>
        Datos de Receptor
        <hr>
        <label
          for="idn_type"
          class="label-base">Tipo de Identificación</label>
        <v-select
          id="idn_type"
          :searchable="false"
          :clearable="false"
          :options="idnTypes"
          v-model="client.idn_type"
          name="idn_type"
          class="mb-3"/>
        <label
          for="idn"
          class="label-base">Numero de Identificación</label>

        <input
          id="idn"
          v-model="receiver.idn"
          type="text"
          class="input-base"
          name="idn"
          required
          autofocus>
        <label
          for="name"
          class="label-base">Nombre</label>

        <input
          id="name"
          v-model="receiver.name"
          type="text"
          class="input-base"
          name="name"
          required
          autofocus
        >

        <label
          for="last_name"
          class="label-base">Apellido(s)</label>

        <input
          id="last_name"
          v-model="receiver.last_name"
          type="text"
          class="input-base"
          name="last_name"
          required
          autofocus>
        <label
          for="phone"
          class="label-base">Telefono</label>

        <input
          id="phone"
          v-model="receiver.phone"
          type="text"
          class="input-base"
          name="phone"
          required
          autofocus>
        <label
          class="label-base"
          for="email">Email</label>
        <input
          id="email"
          v-model="receiver.email"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
        <label
          class="label-base"
          for="email_confirmation">Confirmacion de Email</label>
        <input
          id="email_confirmation"
          v-model="receiver.email_confirmation"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
        <label
          for="address"
          class="label-base">Dirección</label>
        <textarea
          id="address"
          v-model="receiver.address"
          class="input-base"
          required
          autofocus
          name="address"></textarea>
        Datos de Transaccion
        <hr>
      </div>
      <div class="col-12 col-md-6">
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
      operadoresVenezuela: [],
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT'],
      client: {

      },
      receiver: {

      },
    };
  },
  created() {
    window.axios.get('api/operadores-venezuela').then((response) => {
      this.operadoresVenezuela = response.data;
    });
  },
};
</script>

<style scoped>

</style>
