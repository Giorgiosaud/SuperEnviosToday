<template>
  <section>
    <b-select v-model="statusFilter" placeholder="Seleccione estado">
      <option value="">{{$t('pendingTransactions.STATUS:ALL')}}</option>
      <option value="pending">{{$t('pendingTransactions.STATUS:PENDING')}}</option>
      <option value="approved">{{$t('pendingTransactions.STATUS:APPROVED')}}</option>
      <option value="rejected">{{$t('pendingTransactions.STATUS:REJECTED')}}</option>
    </b-select>
    <b-table
      :data="pendingTransactions"
      :total="query.total"
      :current-page="query.current_page"
      :loading="loading"
      :per-page="query.perPage"
      paginated
      backend-pagination
      backend-filtering
      :striped="true"
      :scrollable="true"
      @page-change="changedPage"
      @filters-change="changedFilter"
      aria-next-label="Next page"
      aria-previous-label="Previous page"
      >

      <b-table-column field="id"
                      label="ID"
                      width="40"
                      numeric
                      sticky
                      v-slot="props">
        {{ props.row.id }}
      </b-table-column>
      <b-table-column field="date"
                      :label="$t('pendingTransactions.DATE')"
                      width="200"
                      v-slot="props"
      >
        {{ props.row.created_at |date}}
      </b-table-column>

      <b-table-column field="client"
                      :label="$t('pendingTransactions.CLIENT:NAME_AND_LAST_NAME')"
                      v-slot="props"
      >
        {{ props.row.client.name }} {{ props.row.client.last_name }}
      </b-table-column>

      <b-table-column field="operator_account"
                      :label="$t('pendingTransactions.FOREIGN_OPERATOR:NAME_AND_LAST_NAME')"
                      v-slot="props"
      >
      {{ props.row.foreign_operator.name }} {{ props.row.foreign_operator.last_name }}

      </b-table-column>

      <b-table-column field="venezuelan_operator"
                      :label="$t('pendingTransactions.VENEZUELAN:NAME_AND_LAST_NAME')"
                      v-slot="props"
      >
        {{ props.row.venezuelan_operator.name }} {{ props.row.venezuelan_operator.last_name }}
      </b-table-column>
      <b-table-column field="foreign_bank"
                      :label="$t('pendingTransactions.FOREIGN:BANK')"
                      v-slot="props"
      >
       {{ props.row.foreign_account.bank.name }}
      </b-table-column>
      <b-table-column field="venezuelan_bank_from"
                      :label="$t('pendingTransactions.VENEZUELAN:BANK_FROM')"
                      v-slot="props"
      >
          {{ props.row.local_operator_account.bank.name }}
      </b-table-column>
      <b-table-column field="venezuelan_bank_to"
                      :label="$t('pendingTransactions.VENEZUELAN:BANK_TO')"
                      v-slot="props"
      >
          {{ props.row.receiver_account.bank.name }}
      </b-table-column>
      <b-table-column field="rate"
                      :label="$t('pendingTransactions.SUGGESTED_RATE')"
                      searchable
                      v-slot="props">
        {{ props.row.rate|rateCurrency(props.row.foreign_account.bank.currency) }}
      </b-table-column>
      <b-table-column field="amount"
                      :label="$t('pendingTransactions.AMOUNT')"
                      searchable
                      v-slot="props">
        {{ props.row.amount |currency(props.row.foreign_account.bank.currency)}}
      </b-table-column>
      <b-table-column field="status"
                      :label="$t('pendingTransactions.STATUS')"
                      v-slot="props"
      >
        <span v-if="props.row.status==='pending'">
            {{$t('pendingTransactions.STATUS:APPROVED')}}
        </span>

        <span v-else-if="props.row.status==='approved'">
                            {{$t('pendingTransactions.STATUS:APPROVED')}}
                            </span>
        <span v-else>
                                {{$t('pendingTransactions.STATUS:REJECTED')}}
                            </span>

      </b-table-column>

      <template slot="empty">
        <section class="section">
          <div class="content has-text-grey has-text-centered">
            <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
            <p>No hay datos coincidentes.</p>
          </div>
        </section>
      </template>
    </b-table>
  </section>
</template>
<script>
import currencyFilter from '../../../currency';

export default {
  name: 'MyPendingTransactionList',
  filters: {
    currency(value, selectedCurrency) {
      const formatOptions = {
        precision: 8, separator: '.', decimal: ',', formatWithSymbol: true,
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
        precision: 8, separator: '.', decimal: ',', formatWithSymbol: true,
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
      window.location.href = `my-pendingTransaction/${this.selected.id}`;
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
      const params = this.getAllUrlParams(document.location.href);
      params.page = this.page;
      Object.assign(params, this.filters);
      this.loading = true;
      try {
        const request = await $http.get('api/my-pending-transaction', { params: { ...params } });
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
    },

  },
};
</script>
<style lang="stylus">
    .b-table .table td.is-sticky{
        color:#00c4a7
    }
</style>
