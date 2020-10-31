<template>
  <div>
    <section class="section">
      <g-chart
        :settings="{ packages: ['annotationchart','corechart', 'table', 'map'], language: 'ES-es' }"
        type="AnnotationChart"
        :data="adjustedDataForGraph"
        :options="chartOptions"
        :events="chartEvents"></g-chart>
    </section>
    <section class="section">

      <div class="columns">
        <div class="column">
          <button class="button field is-info"
                  @click="openAddRateModal">
            {{$t('rates.ADD_CURRENCY')}}

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
            :placeholder="$t('banks.SELECT:CURRENCY')"
            @typing="getFilteredTags">
          </b-taginput>
        </div>
      </div>

      <b-table
        :data="rates"
        :loading="loading"
        :striped="true"
        :total="query.total"
        :opened-detailed="defaultOpenedDetails"
        detailed
        :current-page="query.current_page"
        :per-page="query.per_page"
        :show-detail-icon="true"
        detail-key="id"
        ref="currenciesTable"
        aria-next-label="Next page"
        aria-previous-label="Previous page"
        paginated
        backend-paginatiopn
        backend-filtering
        @filters-change="changedFilter"
        @page-change="changedPage">

        <b-table-column field="id"
                        label="ID"
                        width="40"
                        numeric
                        v-slot="props">
          {{ props.row.id }}
        </b-table-column>

        <b-table-column field="since"
                        :label="$t('rates.SINCE')"
                        v-slot="props">
          {{ props.row.since | timeFormat("dd-MM-yyyy 'a las' h:mm a")}}
        </b-table-column>

        <b-table-column field="amount"
                        :label="$t('rates.AMOUNT')"
                        v-slot="props">
          {{ props.row.amount |rateCurrency(props.row.currency) }}
        </b-table-column>
        <b-table-column field="curerncy"
                        :label="$t('rates.CURRENCY')"
                        v-slot="props">
          {{ props.row.currency.name }}
        </b-table-column>
        <b-table-column field="message"
                        :label="$t('rates.MESSAGE:FIELD')"
                        v-slot="props"
        >
          <div v-html="props.row.message"></div>

        </b-table-column>

        <template slot="detail" slot-scope="props">
          <section>
            <b-field label="Select datetime">
              <b-datetimepicker
                v-model="props.row.since"
                mobile-native
                placeholder="Click to select..."
                icon="calendar-today"
                readonly
                :datepicker="{ showWeekNumber:true }"
                :timepicker="{ enableSeconds:true }">
                <template slot="left">
                  <button class="button is-primary"
                          @click="props.row.since = new Date()">
                    <b-icon icon="clock"></b-icon>
                    <span>Now</span>
                  </button>
                </template>
              </b-datetimepicker>
            </b-field>
            <b-field label="Amount">
              <b-input v-model="props.row.amount"></b-input>
            </b-field>
            <b-field label="Tipo de moneda">
              <b-select placeholder="Tipo de moneda" v-model="props.row.currency_id" expanded>
                <option v-for="currency in allCurrencies" :value="currency.id" :key="currency.id">
                  {{currency.name}}
                </option>
              </b-select>
            </b-field>
            <b-field label="Comentario">
              <quill-editor

                            :id="`comment-${props.row.id}`"
                            v-model.lazy="props.row.message"></quill-editor>

            </b-field>
            <div class="buttons">
              <b-button
                @click="changeRate(props.row.id,
                props.row.since,
                props.row.currency_id,
                props.row.amount,
                props.row.message)"
                :loading="savingRate"
                type="is-info">
                Guardar
              </b-button>
              <b-button
                @click="removeRate(props.row.id)"
                :loading="removingRate"
                type="is-danger">
                Borrar
              </b-button>
            </div>
          </section>

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
      <add-rate @currency-created="loadAsyncData" :currencies="allCurrencies"></add-rate>
    </b-modal>
  </div>
</template>
<script>
import { GChart } from 'vue-google-charts';
import { format } from 'date-fns';
import addRate from './addRate.vue';
import currencyFilter from '../../../currency';
// TODO add date filter
export default {
  name: 'RatesList',
  components: {
    addRate,
    GChart,
  },
  filters: {
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
    timeFormat: (value, dateFormat) => format(value, dateFormat),
  },
  props: {
    ratesQuery: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
    allCurrencies: {
      type: Array,
      default: () => ([]),
    },
  },
  data: () => (
    {
      query: {},
      loading: false,
      filters: {},
      selectedCurrencies: [],
      filteredCurrencies: [],
      defaultOpenedDetails: [],
      savingRate: false,
      isOpenModal: false,
      removingRate: false,
      chartOptions: {
        allowHtml: true,
      },
      chartEvents: {},
      adjustedDataForGraph: [],
    }
  ),
  computed: {
    rates() {
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
  },
  created() {
    this.query = this.ratesQuery;
    this.page = this.query.current_page;
    this.filteredCurrencies = this.allCurrencies;
    this.setAdjustedDataForGraph();
  },
  methods: {
    setAdjustedDataForGraph() {
      const orderedData = this.rates.map((rate) => {
        const newRate = rate;
        newRate.since = new Date(newRate.since);
        return newRate;
      })
        .sort((dataA, dataB) => new Date(dataA.since).valueOf() - new Date(dataB.since).valueOf());
      const differentCurrencies = [...new Set(this.rates.map((rate) => rate.currency.name))];
      const differentCurrenciesIds = [...new Set(this.rates.map((rate) => rate.currency_id))];
      const differentDates = [...new Set(orderedData.map((data) => data.since))];
      const titulos = ['Desde', ...differentCurrencies, ...differentCurrencies];
      const chartData = [titulos];

      differentDates.map((date) => {
        const graphData = [date];
        differentCurrenciesIds.map((currencyId) => {
          const allDataOfCurrency = orderedData.filter((data) => data.currency_id === currencyId);
          const dataToAppend = allDataOfCurrency.find((data) => date <= data.since);
          const messageToAppend = allDataOfCurrency.find((data) => date === data.since);

          graphData.push(dataToAppend ? dataToAppend.amount : allDataOfCurrency[allDataOfCurrency.length - 1].amount);
          graphData.push(messageToAppend && messageToAppend.message ? messageToAppend.message : undefined);
          return currencyId;
        });
        chartData.push(graphData);
        return date;
      });

      this.adjustedDataForGraph = chartData;
    },
    getFilteredTags(text) {
      this.filteredCurrencies = this.allCurrencies
        .filter((currency) => currency.name
          .toString()
          .toLowerCase()
          .indexOf(text.toLowerCase()) >= 0);
    },
    openAddRateModal() {
      this.isOpenModal = true;
    },
    async removeRate(id) {
      this.$buefy.dialog.confirm({
        message: '¿Estás seguro de borrar esta tasa de cambio?',
        onConfirm: async () => {
          this.removingCurrency = true;
          try {
            await $http.delete(`api/rates/${id}`);
          } finally {
            this.removingCurrency = false;
            await this.loadAsyncData();
          }
        },
      });
    },
    // eslint-disable-next-line camelcase
    async changeRate(id, since, currency_id, amount, message) {
      this.savingSetting = true;
      try {
        await $http.patch(`api/rates/${id}`, {
          since,
          currency_id,
          amount,
          message,
        });
      } finally {
        this.savingSetting = false;
        await this.loadAsyncData();
      }
    },
    toggle(row) {
      this.$refs.currenciesTable.toggleDetails(row);
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
      if (this.selectedCurrencies.length) {
        params.currencies = this.selectedCurrencies.map((currency) => currency.id);
      }
      this.loading = true;
      const request = await $http.get('/api/rates', { params: { ...params } });
      this.query = await request.json();
      this.loading = false;
      this.setAdjustedDataForGraph();
    },
  },
};
</script>
