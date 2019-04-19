
<template>
  <div class="container">
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
            </tr>
            <tr
              v-for="(venezuelan_account,vacindex) in operador.accounts"
              :key="vacindex"
            >
              <td>{{ venezuelan_account.bank.name }}</td>
              <td>{{ venezuelan_account.number }}</td>
              <td>{{ venezuelan_account.Balance|currency }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  name: 'VenezuelanAccounts',
  data() {
    return {
      operadoresVenezuela: [],
    };
  },
  created() {
    this.getVenezuelanAccounts();
  },
  mounted() {
    Echo.private('transaction-assigned')
      .listen('TransactionExecuted', (e) => {
        console.log(e);
        this.updateOperatorBalance();
      });
  },
  methods: {
    getVenezuelanAccounts() {
      return axios.get('api/operadores-venezuela').then((response) => {
        this.operadoresVenezuela = response.data;
      });
    },
    updateOperatorBalance() {
      axios.get('api/operadores-venezuela').then((response) => {
        this.operadoresVenezuela = response.data;
      });
    },
  },
};
</script>

<style scoped>

</style>
