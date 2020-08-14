<template>
  <div class="container">
    <div class="row operadores_venezuela">
      <div class="col-12">
        <h2>Cuentas y Operadores Foraneos Disponibles</h2>
        <button
          class="btn btn-primary"
          @click="updateVenezuelanAccounts"
          @keyup.esc="updateVenezuelanAccounts"
        >
          Actualizar
        </button>
        <hr>
      </div>
      <div
        class="col-12"
      >
        <div class="table-responsive">
          <table class="table">
            <tr
              v-for="(account, accIndex) in cuentasVenezuela"
              :key="accIndex"
            >
              <td>{{ account.bank.name }}</td>
              <td>{{ account.number }}</td>
              <td>{{ account.Balance|currency }}</td>
              <td>
                Personas Asociadas a la cuenta:
                <ul>
                  <li
                    v-for="owner in account.owners"
                    :key="owner.id"
                  >
                    {{ owner.name }}{{ owner.last_name }}
                  </li>
                </ul>
              </td>
              <td>
                <button
                  class="btn btn-danger"
                  @click="removeIsOperator(account)"
                >
                  Eliminar
                </button>
              </td>
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
      cuentasVenezuela: [],
    };
  },
  created() {
    this.updateVenezuelanAccounts();
  },
  mounted() {
    Echo.private('transaction-assigned')
      .listen('TransactionExecuted', () => {
        this.updateVenezuelanAccounts();
      });
  },
  methods: {
    updateVenezuelanAccounts() {
      axios.get('api/cuentas-foraneas').then((response) => {
        this.cuentasVenezuela = response.data;
      });
    },
    removeIsOperator({id,bank,number}) {
      const bankName = bank.name;
      const response = confirm(`seguro desea eliminar la cuenta numero ${number} del banco ${bankName}`);
      if (response) {
        axios.get(`api/account-remove-operator/${id}`).then(() => {
          this.updateVenezuelanAccounts();
        });
      }
    },
  },
};
</script>

<style scoped>

</style>
