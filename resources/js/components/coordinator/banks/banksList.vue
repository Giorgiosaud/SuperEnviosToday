<template>
  <div>
    <section class="section">

      <div class="columns">
        <div class="column">
          <button class="button field is-info"
                  @click="openAddBankModal">
            {{$t('banks.ADD_BANK')}}

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
        :data="banks"
        :loading="loading"
        :striped="true"
        :total="query.total"
        :opened-detailed="defaultOpenedDetails"
        detailed
        :current-page="query.current_page"
        :per-page="query.per_page"
        :show-detail-icon="true"
        detail-key="id"
        ref="banksTable"
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
                        :label="$t('banks.NAME')"
                        searchable
                        v-slot="props">
          {{ props.row.name }}
        </b-table-column>

        <b-table-column field="currency"
                        :label="$t('banks.CURRENCY')"
                        v-slot="props">
          {{ props.row.currency.name }}
        </b-table-column>

        <template slot="detail" slot-scope="props">
          <section>
            <div class="title">{{$t('banks.EDIT')}}</div>
            <b-field label="Name">
              <b-input v-model="props.row.name"></b-input>
            </b-field>
            <div class="buttons">
              <b-button
                @click="changeName(props.row.id,props.row.name)"
                :loading="savingName"
                type="is-info">
                Guardar
              </b-button>
              <b-button
                @click="removeBank(props.row.id)"
                :loading="removingBank"
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
      <add-bank :currencies='allCurrencies' @bank-saved="loadAsyncData"></add-bank>
    </b-modal>
  </div>
</template>
<script>
import addBank from './addBank.vue';

export default {
  name: 'BankLists',
  components: {
    addBank,
  },
  props: {
    banksQuery: {
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
      selected: {},
      filters: {},
      selectedCurrencies: [],
      filteredCurrencies: [],
      defaultOpenedDetails: [5],
      savingName: false,
      isOpenModal: false,
      removingBank: false,
    }
  ),
  computed: {

    currencies() {
      return this.allCurrencies.map((currency) => currency.name);
    },
    banks() {
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
    this.query = this.banksQuery;
    this.page = this.query.current_page;
    this.filteredCurrencies = this.allCurrencies;
  },
  methods: {
    openAddBankModal() {
      this.isOpenModal = true;
    },
    async removeBank(id) {
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
        await $http.patch(`api/banks/${id}`, {
          name,
        });
      } finally {
        await this.loadAsyncData();
        this.savingName = false;
      }
    },
    toggle(row) {
      this.$refs.banksTable.toggleDetails(row);
    },
    changedPage(page) {
      this.page = page;
      this.loadAsyncData();
    },
    getFilteredTags(text) {
      this.filteredCurrencies = this.allCurrencies
        .filter((currency) => currency.name
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
      this.loading = true;
      const request = await $http.get('/api/banks', { params: { ...params } });
      this.query = await request.json();
      this.loading = false;
    },
  },
};
</script>
