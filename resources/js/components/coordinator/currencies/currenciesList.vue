<template>
  <div>
    <section class="section">

      <div class="columns">
        <div class="column">
          <button class="button field is-info"
                  @click="openAddCurrencyModal">
            {{$t('currencies.ADD_CURRENCY')}}

          </button>
        </div>
      </div>

      <b-table
        :data="currencies"
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

        <b-table-column field="name"
                        :label="$t('currencies.NAME')"
                        searchable
                        v-slot="props">
          {{ props.row.name }}
        </b-table-column>

        <b-table-column field="identifier"
                        :label="$t('currencies.CURRENCY')"
                        v-slot="props">
          {{ props.row.identifier }}
        </b-table-column>
        <b-table-column field="sign"
                        :label="$t('currencies.SIGN')"
                        v-slot="props">
          {{ props.row.sign }}
        </b-table-column>

        <template slot="detail" slot-scope="props">
          <section>
            <b-field label="Name">
              <b-input v-model="props.row.name"></b-input>
            </b-field>
            <b-field label="identifier">
              <b-input v-model="props.row.identifier"></b-input>
            </b-field>
            <b-field label="sign">
              <b-input v-model="props.row.sign"></b-input>
            </b-field>
            <div class="buttons">
              <b-button
                @click="changeCurrency(props.row.id,props.row.name,props.row.identifier,props.row.sign)"
                :loading="savingCurrency"
                type="is-info">
                Guardar
              </b-button>
              <b-button
                @click="removeCurrency(props.row.id)"
                :loading="removingCurrency"
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
      <add-currency @currency-created="loadAsyncData"></add-currency>
    </b-modal>
  </div>
</template>
<script>
import addCurrency from './addCurrency.vue';

export default {
  name: 'CurrenciesList',
  components: {
    addCurrency,
  },
  props: {
    currenciesQuery: {
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
      filters: {},
      defaultOpenedDetails: [],
      savingCurrency: false,
      isOpenModal: false,
      removingCurrency: false,
    }
  ),
  computed: {
    currencies() {
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
  created() {
    this.query = this.currenciesQuery;
    this.page = this.query.current_page;
  },
  methods: {
    openAddCurrencyModal() {
      this.isOpenModal = true;
    },
    async removeCurrency(id) {
      this.$buefy.dialog.confirm({
        message: 'Continue on this task?',
        onConfirm: async () => {
          this.removingCurrency = true;
          try {
            await $http.delete(`api/currencies/${id}`);
          } finally {
            this.removingCurrency = false;
            await this.loadAsyncData();
          }
        },
      });
    },
    async changeCurrency(id, name, identifier, sign) {
      this.savingCurrency = true;
      try {
        await $http.patch(`api/currencies/${id}`, {
          name,
          identifier,
          sign,
        });
      } finally {
        this.savingCurrency = false;
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
      this.loading = true;
      const request = await $http.get('/api/currencies', { params: { ...params } });
      this.query = await request.json();
      this.loading = false;
    },
  },
};
</script>
