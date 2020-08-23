<script>
import currencyFilter from '../../../currency';

const bsFormat = {
  symbol: 'Bs ', precision: 2, separator: '.', decimal: ',', formatWithSymbol: true,
};

export default {
  name: 'VenezuelanOperatorData',
  filters: {
    currency(value) {
      return currencyFilter(value, bsFormat);
    },
  },
  components: {
  },
  props: {
    transactionData: {
      type: [Object, null],
      default: null,
    },
  },
  data: () => ({
    venezuelanAccounts: [],
    selectedAccount: null,
    selectedOperator: null,
    loadingAccounts: true,
  }),
  computed: {
    owners() {
      if (this.selectedAccount) {
        return this.selectedAccount.owners;
      }
      return [];
    },
  },
  watch: {
    selectedAccount() {
      this.selectedOperator = null;
    },
  },
  async created() {
    await this.getBaseAccounts();
  },
  methods: {
    async getBaseAccounts() {
      this.loadingAccounts = true;
      const response = await $http.get('/api/accounts/base');
      this.venezuelanAccounts = await response.json();
      this.loadingAccounts = false;
    },
    selectAccount(account) {
      this.selectedAccount = account;
    },
    nextStep() {
      this.$emit('operator-set', {
        operator: this.selectedOperator,
        selectedAccount: this.selectedAccount,
      });
    },
  },
};
</script>

<style scoped>

</style>
