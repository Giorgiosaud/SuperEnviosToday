
<template>
  <div class="container">
    <div class="row operadores_venezuela">
      <div class="col-12">
        <h2>Operadores Foraneos Disponibles</h2>
        <button
          class="btn btn-primary"
          @click="updateOperatorBalance"
        >
          Actualizar
        </button>
        <hr>
      </div>
      <div
        v-for="(operador, opforindex) in operadoresForaneos"
        :key="opforindex"
        class="col-12"
      >
        <h3>{{ operador.name }} {{ operador.last_name }}</h3>
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
                Moneda
              </th>
              <th>
                Saldo
              </th>
            </tr>
            <tr
              v-for="(foreign_account,facindex) in operador.accounts"
              :key="facindex"
            >
              <td>{{ foreign_account.bank.name }}</td>
              <td>{{ foreign_account.number }}</td>
              <td>{{ foreign_account.bank.currency.identificator }}</td>
              <td>{{ foreign_account.Balance|currency }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>


<script>
export default {
  name: 'ForeignAccounts',
  data() {
    return {
      operadoresForaneos: [],
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
      return axios.get('api/foreign_operators').then((response) => {
        this.operadoresForaneos = response.data;
      });
    },
    updateOperatorBalance() {
      axios.get('api/foreign_operators').then((response) => {
        this.operadoresForaneos = response.data;
      });
    },
  },
};
</script>

<style scoped>

</style>
