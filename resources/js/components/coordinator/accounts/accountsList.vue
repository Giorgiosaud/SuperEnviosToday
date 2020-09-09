<script>
import addAccount from './addAccount.vue';
import addOperator from './addOperator.vue';
import currencyFilter from '../../../currency';

export default {
  name: 'AccountsLists',
  filters: {
    currencyFilter,
  },
  components: {
    addAccount,
    addOperator
  },
  props: {
    accountsQuery: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
    allCurrencies: {
      type: Array,
      default: () => ([]),
    },
    allBanks: {
      type: Array,
      default: () => ([]),
    },
  },
  data: () => (
    {
      query: {},
      loading: false,
      selected: {},
      filters: {},
      selectedCurrencies: [],
      selectedBanks: [],
      filteredCurrencies: [],
      filteredBanks: [],
      defaultOpenedDetails: [5],
      savingName: false,
      isOpenModal: false,
      modal: '',
      removingAccount: false,
      accountToAsociate: null,
      actualOwnersOfAccount: null,
      isRemovingAccountStatus:false
    }
  ),
  computed: {

    currencies() {
      return this.allCurrencies.map((currency) => currency.name);
    },
    accounts() {
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
    selectedCurrencies() {
      this.loadAsyncData();
    },
    selectedBanks() {
      this.loadAsyncData();
    },
  },
  created() {
    this.query = this.accountsQuery;
    this.page = this.query.current_page;
    this.filteredCurrencies = this.allCurrencies;
    this.filteredBanks = this.allBanks;
  },
  methods: {
    async removeAccountStatus(accountID){
      debugger;
      try {
        this.isRemovingAccountStatus = true;
        await $http.patch(`/api/account/${accountID}/toggle-operator-state`)
      } catch(error){
        debugger;
      }finally {
        this.isRemovingAccountStatus = false;
        window.location.reload()
      }
    },
    async addOperatorToAccount({operator, account}) {
      this.isOpenModal = false;
      try {
        await $http.post(`/api/accounts/link/${operator.id}`, {
          ...account
        })
      } finally {
        await this.loadAsyncData()
      }
    },
    asociateToAccount(account, owners) {
      this.modal = 'operator'
      this.accountToAsociate = account;
      this.actualOwnersOfAccount = owners.map(owner => owner.id)
      this.isOpenModal = true;
    },
    async unBind(operator, account) {
      this.$buefy.dialog.confirm({
        message: `¿Desea remover a ${operator.name} ${operator.last_name} de la cuenta del banco ${account.bank.name} numero ${account.number} ?`,
        onConfirm: async () => {
          try {
            await $http.patch(`/api/account/${account.id}/user/${operator.id}/unbind`)
          } finally {
            this.loadAsyncData()
          }
        },
      });
    },
    deleteAccount(id) {
      console.log(id)
    },
    openAddAccountModal() {
      this.modal = 'account'
      this.isOpenModal = true;
    },
    async removeAccount(id) {
      this.$buefy.dialog.confirm({
        message: 'Continue on this task?',
        onConfirm: async () => {
          this.removingBank = true;
          try {
            await $http.delete(`api/banks/${id}`);
          } finally {
            this.removingBank = false;
            await this.loadAsyncData();
          }
        },
      });
    },
    async changeName(id, name) {
      this.savingName = true;
      try {
        await $http.patch(`api/accounts/${id}`, {
          name,
        });
      } finally {
        await this.loadAsyncData();
        this.savingName = false;
      }
    },
    toggle(row) {
      this.$refs.accountsTable.toggleDetails(row);
    },

    changedPage(page) {
      this.page = page;
      this.loadAsyncData();
    },
    getFilteredCurrenciesTags(text) {
      this.filteredCurrencies = this.allCurrencies
        .filter((currency) => currency.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0);
    },
    getFilteredBanksTags(text) {
      this.filteredBanks = this.allBanks
        .filter((bank) => bank.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0);
    },
    async loadAsyncData() {
      const params = this.getAllUrlParams(document.location.href)

      params.page = this.page;
      Object.assign(params, this.filters);
      if (this.selectedCurrencies.length) {
        params.currencies = this.selectedCurrencies.map((currency) => currency.id);
      }
      if (this.selectedBanks.length) {
        params.banks = this.selectedBanks.map((bank) => bank.id);
      }
      this.loading = true;
      const request = await $http.get('/api/accounts', {params: {...params}});
      this.query = await request.json();
      this.loading = false;
    },
  },
};
</script>
