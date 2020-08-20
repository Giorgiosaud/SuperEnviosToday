<script>
import currencyFilter from '../../../currency';

export default {
  name: 'MyTransactions',
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
    transactionsQuery: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
    currency: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
    currencies: {
      type: Array,
      default: () => ({
        data: [],
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
    }
  ),
  computed: {
    selectedCurrency() {
      return this.currencies.find((currency) => currency.id === this.selectedCurrencyId);
    },
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
      this.changedFilter({ status: value });
      this.loadAsyncData();
    },
    selectedCurrencyId(value) {
      this.page = 1;
      this.changedFilter({ currency: value });

      this.loadAsyncData();
    },
  },
  created() {
    this.query = this.transactionsQuery;

    this.page = this.query.current_page;
  },
  methods: {
    transactionClicked(transaction) {
      if (transaction.id === this.selected.id) {
        this.selected = {};
      }
    },
    goToDetails() {
      window.location.href = `transactions/${this.selected.id}`;
    },
    paramsToObject(entries) {
      const result = {};
      entries.forEach((entry) => {
        const [key, value] = entry;
        result[key] = value;
      });
      return result;
    },
    changedPage(page) {
      this.page = page;
      this.loadAsyncData();
    },
    changedFilter(filters) {
      filters.forEach((filter) => {
        if (filters[filter] === '') {
          // eslint-disable-next-line no-param-reassign
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
      const urlParams = new URLSearchParams(document.location.search.substring(1));
      const entries = urlParams.entries();
      const params = this.paramsToObject(entries);
      params.page = this.page;
      params.currency = this.selectedCurrency.id;
      Object.assign(params, this.filters);
      this.loading = true;
      const request = await $http.get('/api/my-transactions', { params: { ...params } });
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
