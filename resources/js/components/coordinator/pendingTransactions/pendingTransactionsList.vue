<script>
import currencyFilter from '../../../currency';

export default {
  name: 'PendingTransactionList',
  filters: {
    currency(value, selectedCurrency) {
      const formatOptions = {
        precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
      };
      if (!selectedCurrency) {
        formatOptions.symbol = '$ ';
      } else {
        formatOptions.symbol = `${selectedCurrency.sign} `;
      }
      return currencyFilter(value, formatOptions);
    },
    rateCurrency: (value, selectedCurrency) => {
      const formatOptions = {
        precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
      };
      if (!selectedCurrency) {
        formatOptions.symbol = 'Bs/$ ';
      } else {
        formatOptions.symbol = `Bs/${selectedCurrency.sign} `;
      }
      return currencyFilter(value, formatOptions);
    },
    date(date) {
      return new Date(date).toLocaleString();
    },
  },
  props: {
    pendingTransactionsQuery: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
  },
  data: () => (
    {
      query: {},
      loading: false,
      selected: {},
      filters: {},
      statusFilter: '',
      onChangeState: false,
    }
  ),
  computed: {
    pendingTransactions() {
      return this.query.data;
    },
    currentPage() {
      return this.query.currentPage ? this.query.currentPage : 0;
    },
    lastPage() {
      return this.query.last_page ? this.query.last_page : 0;
    },
    path() {
      return this.query.path ? this.query.path : '';
    },
  },
  watch: {
    statusFilter(value) {
      this.changedFilter({ status: value });
      this.loadAsyncData();
    },
  },
  created() {
    this.query = this.pendingTransactionsQuery;
    this.page = this.query.current_page;
  },
  methods: {
    pendingTransactionClicked(pendingTransaction) {
      if (pendingTransaction.id === this.selected.id) {
        this.selected = {};
      }
    },
    goToDetails() {
      window.location.href = `pendingTransaction/${this.selected.id}`;
    },
    paramsToObject(entries) {
      const result = {};
      entries.forEach((entry) => { // each 'entry' is a [key, value] tupple
        const [key, value] = entry;
        result[key] = value;
      });
      return result;
    },
    changedPage(page) {
      this.page = page;
      this.loadAsyncData();
    },

    async loadAsyncData() {
      const params =this.getAllUrlParams(document.location.href)
      params.page = this.page;
      Object.assign(params, this.filters);
      this.loading = true;
      try {
        const request = await $http.get('api/pending-transaction', { params: { ...params } });
        this.query = await request.json();
      } catch (error) {
        this.$buefy.notification.open({
          message: `Rechazo fallido message:${JSON.stringify(error.response.data.errors)}`,
          type: 'is-warning',
          position: 'is-bottom-right',
          duration: 5000,

        });
      }
      this.loading = false;
    },
    getFilteredTags(text) {
      this.filteredRoles = this.allRoles
        .filter((role) => role.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0);
      console.log(this.filteredRoles);
    },
    async approveTransaction(transaction) {
      this.onChangeState = true;
      // const data = { accept_transaction: false };
      try {
        const { data } = await axios.patch(`api/pending-transaction/${transaction.id}`,
          {
            accept_transaction: true,
          });
        this.$buefy.notification.open({
          message: `Transacción #${data.id} Aprovada`,
          type: 'is-success',
          position: 'is-bottom-right',
          duration: 5000,
        });
        // eslint-disable-next-line no-param-reassign
        transaction.status = 'approved';
      } catch (error) {
        this.$buefy.notification.open({
          message: `Rechazo fallido message:${error.message}`,
          type: 'is-warning',
          position: 'is-bottom-right',
          duration: 5000,
        });
      }
      this.onChangeState = false;
    },
    async rejectTransation(transaction) {
      this.onChangeState = true;
      // const data = { accept_transaction: false };
      try {
        const { data } = await axios.patch(`api/pending-transaction/${transaction.id}`,
          {
            accept_transaction: false,
          });
        this.$buefy.notification.open({
          message: `Transacción #${data.id} Rechazada`,
          type: 'is-success',
          position: 'is-bottom-right',
          duration: 5000,
        });
        // eslint-disable-next-line no-param-reassign
        transaction.status = 'rejected';
      } catch (error) {
        this.$buefy.notification.open({
          message: `Rechazo fallido message:${error.message}`,
          type: 'is-warning',
          position: 'is-bottom-right',
          duration: 5000,
        });
      }
      this.onChangeState = false;
    },
  },
};
</script>
<style lang="stylus">
    .b-table .table td.is-sticky{
        color:#00c4a7
    }
</style>
