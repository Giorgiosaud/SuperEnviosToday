<template>
  <section>
    <div class="columns">
      <div class="column">
        <b-select v-model="statusFilter" placeholder="Seleccione estado">
          <option value="">{{ $t('transaction.STATUS:ALL') }}</option>
          <option value="pending">{{ $t('transaction.STATUS:PENDING') }}</option>
          <option value="executed">{{ $t('transaction.STATUS:APPROVED') }}</option>
          <option value="in-progress">{{ $t('transaction.STATUS:IN:PROGRESS') }}</option>
        </b-select>
      </div>
      <div class="column">
        <b-select v-model="accountFilter" placeholder="Seleccione Cuenta">
          <option value="">{{ $t('transaction.STATUS:ALL') }}</option>
          <option v-for="account in accounts"
                  :key="account.id"
                  :value="account.id"
          >
            @{{ account.bank.name }} / @{{ account.number }}
          </option>
        </b-select>
      </div>
      <div class="column is-narrow">
        <b-button type="is-info" outlined @click="refreshData">{{ $t('transaction.REFRESH') }}</b-button>
      </div>
    </div>

    <b-table
      ref="mainTable"
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
      @details-open="getDetailsData"
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
                      v-slot="prop"
      >
        {{ prop.row.bank_reference }}
      </b-table-column>
      <b-table-column field="track_number"
                      :label="$t('transaction.TRACKING:NUMBER')"
                      width="200"
                      v-slot="prop"
      >
        {{ prop.row.track_number }}
      </b-table-column>
      <b-table-column field="operator"
                      :label="$t('transaction.OPERATOR')"
                      width="200"
                      v-slot="prop"
      >
        {{ prop.row.operator.name }} @{{ prop.row.operator.last_name }}
      </b-table-column>
      <b-table-column field="client_id"
                      :label="$t('transaction.CLIENT')"
                      v-slot="prop"
                      width="200"


      ><span v-if="prop.row.client">
                            @{{ prop.row.client.name }} @{{ prop.row.client.last_name }}
                            </span>
        <span v-else>N/A</span>
      </b-table-column>
      <b-table-column field="amount"
                      :label="$t('transaction.AMOUNT')"
                      v-slot="prop">
        @{{ prop.row.amount |currency(prop.row.account.bank.currency) }}
      </b-table-column>
      <b-table-column field="status"
                      :label="$t('transaction.STATUS')"
                      v-slot="prop">
        <span v-if="prop.row.status=='pending'">{{ $t('transaction.STATUS:PENDING') }}</span>
        <span v-else-if="prop.row.status=='executed'">{{ $t('transaction.STATUS:APPROVED') }}</span>
        <span
          v-else-if="prop.row.status=='in-progress'">{{ $t('transaction.STATUS:IN:PROGRESS') }}</span>
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
                       :can-cancel="false">
            </b-loading>
            <section
              v-if="!isLoadingTransaction &&
              transaction &&
              transaction.venezuelanRelated &&
              transaction.venezuelanRelated.status &&transaction.venezuelanRelated.status==='executed'"
              class="section">
              <div class="columns is-desktop">
                <div class="column is-half is-offset-one-quarter">
                  <div class="card">
                    <div class="card-image" v-if="transaction.attachments.length">
                      <b-carousel>
                        <b-carousel-item
                          v-for="attachment in transaction.attachments"
                          :key="attachment.id">
                          <b-image
                            :src="`${appUrl}${attachment.path}`"
                            :placeholder="attachment.updated_at"
                            ratio="2by1"
                          ></b-image>
                        </b-carousel-item>
                      </b-carousel>
                    </div>
                    <div class="card-content">
                      <div class="media">
                        <div class="media-content">
                          <p class="title is-4">Datos de La Transacción</p>
                          <p class="subtitle is-6">
                            {{$t('transaction.BANK:REFERENCE')}}: {{transaction.venezuelanRelated.bank_reference}}
                          </p>
                        </div>
                      </div>

                      <div class="content">
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.CLIENT:NAME_AND_LAST_NAME')}}</div>
                          <div class="level-right">
                            {{transaction.venezuelanRelated.account.owners[0].name}}
                            {{transaction.venezuelanRelated.account.owners[0].last_name}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.IDN')}}</div>
                          <div class="level-right">
                            {{transaction.venezuelanRelated.account.owners[0].idn_type}}-{{transaction.venezuelanRelated.account.owners[0].idn}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.PHONE')}}</div>
                          <div class="level-right">
                            {{transaction.venezuelanRelated.account.owners[0].phone}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.EMAIL')}}</div>
                          <div class="level-right">
                            {{transaction.venezuelanRelated.account.owners[0].email}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('banks.MENU:TITLE')}}</div>
                          <div class="level-right">
                            {{transaction.venezuelanRelated.account.bank.name}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('accounts.NUMBER')}}</div>
                          <div class="level-right">
                            {{transaction.venezuelanRelated.account.number|account}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.CREATED_AT')}}</div>
                          <div class="level-right">
                            <time :datetime="transaction.venezuelanRelated.created_at">
                              {{transaction.venezuelanRelated.created_at|datetime}}
                            </time>
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.UPDATED_AT')}}</div>
                          <div class="level-right">
                            <time :datetime="transaction.venezuelanRelated.updated_at">
                              {{transaction.venezuelanRelated.updated_at|datetime }}
                            </time>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </section>
            <section
              v-if="!isLoadingTransaction &&
              transaction &&
              transaction.venezuelanRelated &&
              transaction.venezuelanRelated.status &&
              transaction.venezuelanRelated.status!=='executed'"
              class="section">
              <div class="columns is-desktop">
                <div class="column is-half is-offset-one-quarter">
                  <validation-observer v-slot="{invalid}" class="card" tag="div">
                    <div class="card-image" >
                      <validation-provider
                        rules="required|min:1"
                        :name="$t('transaction.VOUCHER:FILES')"
                        v-slot="{ classes,errors,valid}"
                        tag="div"
                        class="control">
                        <uppy-uploader
                          :class="classes"
                          v-model="transaction.venezuelanRelated.attachments"
                          :max-file-size-in-bytes="1000000">
                        </uppy-uploader>
                        <strong v-if="errors[0]"
                                class="help is-danger">@{{errors[0]}}</strong>
                      </validation-provider>
                    </div>
                    <div class="card-content">
                      <div class="media">
                        <div class="media-content">
                          <p class="title is-4">Datos de La Transacción</p>
                          <b-field :label="$t('transaction.BANK:REFERENCE')">
                            <validation-provider
                              rules="required"
                              :name="$t('transaction.BANK:REFERENCE')"
                              v-slot="{ classes,errors,valid}">
                              <b-input
                                :class="classes"

                                v-model="transaction.venezuelanRelated.bank_reference"></b-input>
                            </validation-provider>
                          </b-field>
                        </div>
                      </div>

                      <div class="content">
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.CLIENT:NAME_AND_LAST_NAME')}}</div>
                          <div class="level-right">
                            @{{transaction.venezuelanRelated.account.owners[0].name}}
                            @{{transaction.venezuelanRelated.account.owners[0].last_name}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.IDN')}}</div>
                          <div class="level-right">
                            @{{transaction.venezuelanRelated.account.owners[0].idn_type}}-@{{transaction.venezuelanRelated.account.owners[0].idn}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.PHONE')}}</div>
                          <div class="level-right">
                            @{{transaction.venezuelanRelated.account.owners[0].phone}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('auth.EMAIL')}}</div>
                          <div class="level-right">
                            @{{transaction.venezuelanRelated.account.owners[0].email}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('banks.MENU:TITLE')}}</div>
                          <div class="level-right">
                            @{{transaction.venezuelanRelated.account.bank.name}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('accounts.NUMBER')}}</div>
                          <div class="level-right">
                            @{{transaction.venezuelanRelated.account.number|account}}
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.CREATED_AT')}}</div>
                          <div class="level-right">
                            <time :datetime="transaction.venezuelanRelated.created_at">
                              @{{
                                transaction.venezuelanRelated.created_at|datetime }}
                            </time>
                          </div>
                        </div>
                        <div class="level p-0">
                          <div class="level-left">{{$t('transaction.UPDATED_AT')}}</div>
                          <div class="level-right">
                            <time :datetime="transaction.venezuelanRelated.updated_at">
                              @{{
                                transaction.venezuelanRelated.updated_at|datetime }}
                            </time>
                          </div>
                        </div>
                        <br>


                      </div>
                    </div>
                    <footer class="card-footer">
                      <b-button
                        type="is-primary"
                        :disabled="invalid"
                        :loading="isExecutingTransaction"
                        @click="executeTransaction(transaction.venezuelanRelated)"
                      >Ejecutar
                      </b-button>
                    </footer>
                  </validation-observer>
                </div>
              </div>
            </section>
          </td>
        </tr>
      </template>
      <!--template slot="empty">
        <section class="section">
          <div class="content has-text-grey has-text-centered">
            <font-awesome-icon class="is-size-1" icon="sad-tear"></font-awesome-icon>
            <p>No hay datos coincidentes.</p>
          </div>
        </section>
      </template-->
    </b-table>
  </section>
</template>
<script>
import currencyFilter from '../../../currency';
import UppyUploader from '../../../UppyUploader.vue';
import {format, parseISO} from 'date-fns'

export default {
  name: 'MyVenezuelanTransactions',
  components: {
    UppyUploader
  },
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
    appUrl:{
      type:String,
      default:'#'
    },
    accounts: {
      type: Array,
      default: () => ([])
    },
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
      accountFilter: '',
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
    accountFilter(value) {
      this.changedFilter({account: value});
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
        debugger;
        this.$refs.mainTable.closeDetailRow()
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
      this.filters = {
        ...this.filters,
        ...filters
      }
      this.loadAsyncData();
    },
    refreshData() {
      this.page = 1;
      this.loadAsyncData();
    },
    async loadAsyncData() {
      const params = this.getAllUrlParams(document.location.href)
      params.page = this.page;
      const filters = {
        ...this.filters
      };
      const keys = Object.keys(filters);
      keys.forEach(key => {
        if (filters[key] === '') {
          delete filters[key];
        }
      })
      Object.assign(params, filters);
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
