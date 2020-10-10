<script>
import currencyFilter from '../../../currency';
import UppyUploader from '../../../UppyUploader.vue';

export default {
  name: 'MyVenezuelanTransactions',
  components: {
    UppyUploader
  },
  filters: {
    account(value) {
      const result = value.match(/\d{4}/g);
      return result.join('-');
    },
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
    transactionsQuery: {
      type: Object,
      default: () => ({
        data: []
      }),
    },
    currency: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
  },
  data: () => (
    {
      defaultOpenedDetails: [],
      query: {},
      loading: false,
      selected: {},
      filters: {},
      statusFilter: '',
      onChangeState: false,
      selectedCurrencyId: 1,
      isLoadingTransaction: false,
      isExecutingTransaction: false
    }
  ),
  computed: {
    transactions() {
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
      this.changedFilter({status: value});
      this.loadAsyncData();
    },
    selectedCurrencyId(value) {
      this.page = 1;
      this.changedFilter({currency: value});

      this.loadAsyncData();
    },
  },
  created() {
    this.query = this.transactionsQuery;

    this.page = this.query.current_page;
  },
  methods: {
    async executeTransaction(venezuelanTransaction) {
      this.isExecutingTransaction = true;
      try {
        await $http.patch(`/api/related-venezuelan-transaction`, {transaction: venezuelanTransaction});
      } finally {
        this.isExecutingTransaction = false;
        await this.loadAsyncData();
      }
    },
    async getDetailsData(transaction) {
      this.isLoadingTransaction = true;
      try {
        const request = await $http.get(`/api/related-venezuelan-transactions/${transaction.id}`);
        this.$set(transaction, 'venezuelanRelated', await request.json());

      } finally {
        this.isLoadingTransaction = false;
      }
    },
    transactionClicked(transaction) {
      if (transaction.id === this.selected.id) {
        this.selected = {};
      }
    },
    changedPage(page) {
      this.page = page;
      this.loadAsyncData();
    },
    changedFilter(filters) {
      filters.forEach((filter) => {
        if (filters[filter] === '') {
          delete filters[filter];
        }
      });
      this.filters = filters;
      this.loadAsyncData();
    },
    refreshData() {
      this.page = 1;
      this.loadAsyncData();
    },
    async loadAsyncData() {
      const params = this.getAllUrlParams(document.location.href)
      params.page = this.page;
      Object.assign(params, this.filters);
      this.loading = true;
      const request = await $http.get('/api/my-venezuelan-transactions', {params: {...params}});
      this.query = await request.json();
      this.loading = false;
    },
    getFilteredTags(text) {
      this.filteredRoles = this.allRoles
        .filter((role) => role.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0);
    },

  },
};
</script>
<style lang="stylus">
.b-table .table td.is-sticky {
  color: #00c4a7
}
</style>
