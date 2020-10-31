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
      @details-open="getDetailsData"
      aria-previous-label="Previous page"
    >

      <b-table-column field="id"
                      label="ID"
                      width="40"
                      numeric
                      v-slot="props"
                      sticky>
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
        <span v-else> N/A</span>
      </b-table-column>
      <b-table-column field="amount"
                      :label="$t('transaction.AMOUNT')"
                      v-slot="props"
      >
        {{ props.row.amount |currency(props.row.account.bank.currency)}}
      </b-table-column>
      <b-table-column field="status"
                      :label="$t('transaction.STATUS')"
                      v-slot="props"
      >
        <span v-if="props.row.status=='pending'">{{$t('transaction.STATUS:PENDING')}}</span>
        <span v-else-if="props.row.status=='executed'">{{$t('transaction.STATUS:APPROVED')}}</span>
        <span
          v-else-if="props.row.status=='in-progress'">{{$t('transaction.STATUS:IN:PROGRESS')}}</span>
      </b-table-column>
      <template #detail="{row:transaction}">
        <tr v-if="transaction.comment">
          <td colspan="8">
            <section class="section">
              {{transaction.comment}}
              <br>
            </section>
          </td>
        </tr>
        <tr>
          <td colspan="8">
            <b-loading :is-full-page="false"
                       v-model="isLoadingTransaction"
                       v-if="isLoadingTransaction"
                       :can-cancel="false">
            </b-loading>
            <section
              v-else
              v-for="relatedTransaction in transaction.related"
              :key="relatedTransaction.id"
              class="section">
              <div class="columns is-desktop">
                <div class="column is-half is-offset-one-quarter">
                  <div class="card">
                    <div class="card-image" v-if="relatedTransaction.attachments.length">
                      <b-carousel @click="switchGallery(true)">
                        <b-carousel-item
                          v-for="(attachment,key) in relatedTransaction.attachments"
                          :key="`${attachment.id}-${key}`">
                          <b-image
                            :src="`${appUrl}${attachment.path}`"
                            :placeholder="attachment.updated_at"
                            ratio="2by1"
                            @click="deleteImage(attachment.id)"
                          ></b-image>
                        </b-carousel-item>
                      </b-carousel>
                    </div>
                    <div class="card-content">
                      <div class="media">
                        <div class="media-content">
                          <p class="title is-4">Datos de La Transacción</p>
                          <p class="subtitle is-6">
                            {{$t('transaction.BANK:REFERENCE')}}: {{relatedTransaction.bank_reference}}
                          </p>
                        </div>
                      </div>

                      <div class="content">
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.CLIENT:NAME_AND_LAST_NAME')}}</div>
                          <div class="level-right">
                            {{relatedTransaction.account.owners[0].name}}
                            {{relatedTransaction.account.owners[0].last_name}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.IDN')}}</div>
                          <div class="level-right">
                            {{relatedTransaction.account.owners[0].idn_type}}
                            -{{relatedTransaction.account.owners[0].idn}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.PHONE')}}</div>
                          <div class="level-right">
                            {{relatedTransaction.account.owners[0].phone}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.EMAIL')}}</div>
                          <div class="level-right">
                            {{relatedTransaction.account.owners[0].email}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('banks.MENU:TITLE')}}</div>
                          <div class="level-right">
                            {{relatedTransaction.account.bank.name}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('accounts.NUMBER')}}</div>
                          <div class="level-right">
                            {{relatedTransaction.account.number|account}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.CREATED_AT')}}</div>
                          <div class="level-right">
                            <time :datetime="relatedTransaction.created_at">
                              {{relatedTransaction.created_at|datetime}}
                            </time>
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.UPDATED_AT')}}</div>
                          <div class="level-right">
                            <time :datetime="relatedTransaction.updated_at">
                              {{relatedTransaction.updated_at|datetime }}
                            </time>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
          </td>
        </tr>
      </template>
      <!-- <template slot="detail" slot-scope="props">
        <tr v-for="relatedTransaction in props.row.related" :key="relatedTransaction.id">
          <td></td>
          <td>{{ relatedTransaction.id }}</td>
          <td>{{ relatedTransaction.bank_reference }}</td>
          <td>{{ relatedTransaction.track_number }}</td>
          <td>{{ relatedTransaction.operator.name }} {{ relatedTransaction.operator.last_name }}
          </td>
          <td v-if="relatedTransaction.client">{{ relatedTransaction.client.name }} {{
              relatedTransaction.client.last_name }}
          </td>
          <td v-else>Cuenta Propia</td>
          <td>{{ relatedTransaction.amount |currency(relatedTransaction.account.bank.currency)}}</td>
          <td>
            <span
              v-if="relatedTransaction.status=='pending'">{{$t('transaction.STATUS:PENDING')}}</span>
            <span
              v-else-if="relatedTransaction.status=='executed'">{{$t('transaction.STATUS:APPROVED')}}</span>
            <span
              v-else-if="relatedTransaction.status=='in-progress'">{{$t('transaction.STATUS:IN:PROGRESS')}}</span>
          </td>
        </tr>
      </template> -->
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
import { format, parseISO } from 'date-fns';
import currencyFilter from '../../../currency';

export default {
  name: 'MyTransactions',
  filters: {
    datetime(time) {
      return format(parseISO(time), 'dd-mm-yyyy HH:mm');
    },
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
    appUrl: {
      type: String,
      default: '#',
    },
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
    async getDetailsData(transaction) {
      this.isLoadingTransaction = true;
      try {
        const request = await $http.get(`/api/my-related-transactions/${transaction.id}`);
        this.$set(transaction, 'related', await request.json());
      } finally {
        this.isLoadingTransaction = false;
      }
    },
    async loadAsyncData() {
      const params = this.getAllUrlParams(document.location.href);
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
