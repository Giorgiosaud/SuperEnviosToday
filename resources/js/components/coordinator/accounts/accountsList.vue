<template>
  <div>

    <section class="section">

      <div class="columns">
        <div class="column">
          <button class="button field is-info"
                  @click="openAddAccountModal">
            {{$t('accounts.ADD_ACCOUNT')}}

          </button>
        </div>
        <div class="column">
          <b-taginput
            v-model="selectedCurrencies"
            :data="filteredCurrencies"
            autocomplete
            :allow-new="false"
            :open-on-focus="true"
            field="name"
            icon="label"
            :placeholder="$t('accounts.SELECT:CURRENCY')"
            @typing="getFilteredCurrenciesTags">
          </b-taginput>
        </div>

      </div>

      <b-table
        :data="accounts"
        :loading="loading"
        :striped="true"
        :total="query.total"
        :current-page="query.current_page"
        :per-page="query.per_page"
        ref="accountsTable"
        aria-next-label="Next page"
        aria-previous-label="Previous page"
        paginated
        backend-paginatiopn
        backend-filtering
        @filters-change="changedFilter"
        @page-change="changedPage"
        detailed>
        <b-table-column field="id"
                        label="ID"
                        width="40"
                        numeric
                        v-slot="{row:account}">
          {{ account.id }}
        </b-table-column>

        <b-table-column field="number"
                        :label="$t('accounts.NUMBER')"
                        v-slot="{row:account}">
          {{ account.number }}
        </b-table-column>

        <b-table-column field="name"
                        :label="$t('accounts.BANK:NAME')"
                        v-slot="{row:account}">
          {{ account.bank.name }}
        </b-table-column>
        <b-table-column field="name"
                        :label="$t('accounts.BANK:CURRENCY')"
                        v-slot="{row:account}">
          {{ account.bank.currency.name }}
        </b-table-column>
        <b-table-column field="balance" :label="$t('accounts.BALANCE')"
                        v-slot="{row:account}">
          {{ account.balance |currencyFilter({
          ...account.bank.currency,
          formatWithSymbol:account.bank.currency.format_with_symbol === 1
        })}}
        </b-table-column>
        <b-table-column field="Acciones"
                        :label="$t('accounts.ACTIONS')"
                        v-slot="{row:account}">
          <div class="buttons">

            <button class="button field is-danger"
                    @click="deleteAccount(account.id)">
              {{$t('accounts.DELETE_ACCOUNT')}}

            </button>
            <a class="button field is-info"
               :href="`/accounts/${account.id}`">
              {{$t('accounts.CREATE_ADJUSTMENT_TRANSACTION')}}
            </a>
            <b-button :type="account.is_operator?'is-success':'is-danger'"
                      :loading="isRemovingAccountStatus"
                      @click="removeAccountStatus(account.id)">{{$t('accounts.REMOVE')}}
            </b-button>
          </div>
        </b-table-column>
        <template #detail="{row:account}">
          <article>
            <header>
              <h1 class="is-size-3 has-text-centered">
                Operadores Asociados a esta cuenta
              </h1>
            </header>
            <b-table
              :data="account.owners">

              <b-table-column field="idn_type" label="Tipo de identificación"
                              v-slot="{row:owner}">
                {{ owner.idn_type }}
              </b-table-column>
              <b-table-column field="idn" label="Número" v-slot="{row:owner}">
                {{ owner.idn }}
              </b-table-column>
              <b-table-column field="name" label="Nombres" v-slot="{row:owner}">
                {{ owner.name }}
              </b-table-column>
              <b-table-column field="last_name" label="Apellidos" v-slot="{row:owner}">
                {{ owner.last_name }}
              </b-table-column>
              <b-table-column field="phone" label="Teléfono" v-slot="{row:owner}">
                {{ owner.phone }}
              </b-table-column>

              <b-table-column field="email" label="Email" v-slot="{row:owner}">
                {{owner.email }}
              </b-table-column>
              <b-table-column field="remove" label="Accion" v-slot="{row:owner}">
                <b-button type="is-danger" @click="unBind(owner,account)">Desasociar Operador
                </b-button>
              </b-table-column>
            </b-table>

            <footer>
              <b-button type="is-success" @click="asociateToAccount(account,account.owners)">
                asociar operador
              </b-button>
            </footer>
          </article>
        </template>
        <template #empty>
          <section class="section">
            <div class="content has-text-grey has-text-centered">
              <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
              <p>No hay datos coincidentes.</p>
            </div>
          </section>
        </template>
      </b-table>
    </section>
    <b-modal
      :active.sync="isOpenModal"
      has-modal-card
      trap-focus
      :destroy-on-hide="false"
      aria-role="dialog"
      aria-modal>
      <add-account v-if="modal === 'account'" :currencies='allCurrencies' :banks="allBanks"
                   @account-saved="loadAsyncData"></add-account>
      <add-operator v-else :account="accountToAsociate" :actual-owners="actualOwnersOfAccount"
                    @add-operator-to-account="addOperatorToAccount"></add-operator>

    </b-modal>
  </div>
</template>
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
    addOperator,
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
      isRemovingAccountStatus: false,
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
    async removeAccountStatus(accountID) {
      try {
        this.isRemovingAccountStatus = true;
        await $http.patch(`/api/account/${accountID}/toggle-operator-state`);
      } finally {
        this.isRemovingAccountStatus = false;
        window.location.reload();
      }
    },
    async addOperatorToAccount({ operator, account }) {
      this.isOpenModal = false;
      try {
        await $http.post(`/api/accounts/link/${operator.id}`, {
          ...account,
        });
      } finally {
        await this.loadAsyncData();
      }
    },
    asociateToAccount(account, owners) {
      this.modal = 'operator';
      this.accountToAsociate = account;
      this.actualOwnersOfAccount = owners.map((owner) => owner.id);
      this.isOpenModal = true;
    },
    async unBind(operator, account) {
      this.$buefy.dialog.confirm({
        message: `¿Desea remover a ${operator.name} ${operator.last_name}
        de la cuenta del banco ${account.bank.name} numero ${account.number} ?`,
        onConfirm: async () => {
          try {
            await $http.patch(`/api/account/${account.id}/user/${operator.id}/unbind`);
          } finally {
            this.loadAsyncData();
          }
        },
      });
    },
    deleteAccount(id) {
      // TODO: remove or check
      return id;
    },
    openAddAccountModal() {
      this.modal = 'account';
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
      const params = this.getAllUrlParams(document.location.href);

      params.page = this.page;
      Object.assign(params, this.filters);
      if (this.selectedCurrencies.length) {
        params.currencies = this.selectedCurrencies.map((currency) => currency.id);
      }
      if (this.selectedBanks.length) {
        params.banks = this.selectedBanks.map((bank) => bank.id);
      }
      this.loading = true;
      const request = await $http.get('/api/accounts', { params: { ...params } });
      this.query = await request.json();
      this.loading = false;
    },
  },
};
</script>
