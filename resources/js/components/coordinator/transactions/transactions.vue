<template>
  <section>
    <div class="columns">
      <div class="column">
        <b-select v-model="statusFilter" placeholder="Seleccione estado">
          <option value="">{{$t('transaction.STATUS:ALL')}}</option>
          <option value="pending">{{$t('transaction.STATUS:PENDING')}}</option>
          <option value="executed">{{$t('transaction.STATUS:APPROVED')}}</option>
          <option value="in-progress">{{$t('transaction.STATUS:IN:PROGRESS')}}</option>
        </b-select>
      </div>
      <div class="column">
        <b-select v-model="selectedCurrencyId" placeholder="Seleccione una moneda estado">
          <option v-for="currency in currencies" :key="currency.id" :value="currency.id">
            {{currency.name}}
          </option>
        </b-select>
      </div>
      <div class="column is-narrow">
        <b-button type="is-info" outlined @click="refreshData">{{$t('transaction.REFRESH')}}</b-button>
      </div>
    </div>

    <b-table
      @click="transactionClicked"
      :data="transactions"
      :total="query.total"
      :opened-detailed="defaultOpenedDetails"
      detail-key="id"
      custom-detail-row
      detailed
      :current-page="query.current_page"
      :loading="loading"
      :per-page="query.perPage"
      paginated
      backend-pagination
      backend-filtering
      :show-detail-icon="true"
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
      <b-table-column field="bank_reference"
                      :label="$t('transaction.BANK:REFERENCE')"
                      width="200"
                      v-slot="props"
      >
        {{ props.row.bank_reference}}
      </b-table-column>
      <b-table-column field="track_number"
                      :label="$t('transaction.TRACKING:NUMBER')"
                      width="200"
                      v-slot="props"
      >
        {{ props.row.track_number}}
      </b-table-column>
      <b-table-column field="track_number"
                      :label="$t('transaction.DATE')"
                      width="200"
                      v-slot="props"
      >
        {{ props.row.created_at|datetime}}
      </b-table-column>
      <b-table-column field="operator"
                      :label="$t('transaction.OPERATOR')"
                      width="200"
                      v-slot="props"
      >
        {{ props.row.operator.name}} {{ props.row.operator.last_name}}
      </b-table-column>
      <b-table-column field="client_id"
                      :label="$t('transaction.CLIENT')"
                      width="200"
                      v-slot="props"

      >
        <span v-if="props.row.client">{{ props.row.client.name}} {{ props.row.client.last_name}}</span>
        <span v-else>N/A</span>
      </b-table-column>
      <b-table-column
        field="amount"
        :label="$t('transaction.AMOUNT')"
        v-slot="props"
      >
        {{ props.row.amount |currency(props.row.account.bank.currency)}}
      </b-table-column>
      <b-table-column
        ield="status"
        :label="$t('transaction.STATUS')"
        v-slot="props"
      >
        <span v-if="props.row.status=='pending'">{{$t('transaction.STATUS:PENDING')}}</span>
        <span v-else-if="props.row.status=='executed'">{{$t('transaction.STATUS:APPROVED')}}</span>
        <span
          v-else-if="props.row.status=='in-progress'">{{$t('transaction.STATUS:IN:PROGRESS')}}</span>
      </b-table-column>
      <template slot="detail" slot-scope="props">
        <tr v-if="props.row.comment">
          <td></td>
          <td colspan="7" v-html="props.row.comment"></td>
        </tr>
        <template  v-for="relatedTransaction in props.row.related">
          <tr :key="relatedTransaction.id">
            <td></td>
            <td>{{ relatedTransaction.id }}</td>
            <td>{{ relatedTransaction.bank_reference }}</td>
            <td>{{ relatedTransaction.track_number }}</td>
            <td>{{ relatedTransaction.created_at|datetime}}</td>
            <td>{{ relatedTransaction.operator.name }} {{ relatedTransaction.operator.last_name }}
            </td>
            <td v-if="relatedTransaction.client">{{ relatedTransaction.client.name }} {{
                relatedTransaction.client.last_name }}
            </td>
            <td v-else>Cuenta Propia</td>
            <td>{{ relatedTransaction.amount
              |currency(relatedTransaction.account.bank.currency)}}
            </td>
            <td>
              <span
                  v-if="relatedTransaction.status=='pending'">{{$t('transaction.STATUS:PENDING')}}</span>
              <span
                v-else-if="relatedTransaction.status=='executed'">{{$t('transaction.STATUS:APPROVED')}}</span>
              <span
                v-else-if="relatedTransaction.status=='in-progress'">{{$t('transaction.STATUS:IN:PROGRESS')}}</span>
            </td>
          </tr>
          <tr v-if="relatedTransaction.comment" :key="relatedTransaction.id">
            <td></td>
            <td colspan="7" v-html="relatedTransaction.comment"></td>
          </tr>
        </template>

      </template>
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
import { datetime } from '../../../datetimeFilter';

export default {
  name: 'Transactions',
  filters: {
    datetime,
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
      type: [Array, Object],
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

    refreshData() {
      this.page = 1;
      this.loadAsyncData();
    },
    async loadAsyncData() {
      const params = this.getAllUrlParams(document.location.href);
      params.page = this.page;
      params.currency = this.selectedCurrency.id;
      Object.assign(params, this.filters);
      this.loading = true;
      const request = await $http.get('/api/transactions', { params: { ...params } });
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
