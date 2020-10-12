<template>
  <div>
    <section class="section">

      <div class="columns">
        <div class="column">
          <button class="button field is-info"
                  @click="openAddSettingModal">
            {{$t('settings.ADD_SETTING')}}

          </button>
        </div>
      </div>

      <b-table
        :data="settings"
        :loading="loading"
        :striped="true"
        :total="query.total"
        :opened-detailed="defaultOpenedDetails"
        detailed
        :current-page="query.current_page"
        :per-page="query.per_page"
        :show-detail-icon="true"
        detail-key="id"
        ref="settingsTable"
        aria-next-label="Next page"
        aria-previous-label="Previous page"
        paginated
        backend-paginatiopn
        backend-filtering
        @filters-change="changedFilter"
        @page-change="changedPage">

        <b-table-column
          field="id"
          label="ID"
          width="40"
          numeric
          v-slot="props">
          {{ props.row.id }}
        </b-table-column>

        <b-table-column field="key"
                        :label="$t('settings.KEY')"
                        searchable
                        v-slot="props">
          {{ props.row.key }}
        </b-table-column>

        <b-table-column
          field="value"
          :label="$t('settings.VALUE')"
          v-slot="props">
          {{ props.row.value }}
        </b-table-column>

        <template slot="detail" slot-scope="props">
          <section>
            <b-field label="Key">
              <b-input v-model="props.row.key"></b-input>
            </b-field>
            <b-field label="Value">
              <b-input v-model="props.row.value"></b-input>
            </b-field>
            <div class="buttons">
              <b-button
                @click="changeSetting(props.row.id,props.row.key,props.row.value)"
                :loading="savingSetting"
                type="is-info">
                Guardar
              </b-button>
              <b-button
                @click="removeSetting(props.row.id)"
                :loading="removingSetting"
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
              <p>No hay Configuraciones.</p>
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
      <add-setting @setting-created="loadAsyncData"></add-setting>
    </b-modal>
  </div>
</template>
<script>
import addSetting from './addSetting.vue';

export default {
    name: 'SettingsList',
    components: {
        addSetting,
    },
    props: {
        settingsQuery: {
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
            savingSetting: false,
            isOpenModal: false,
            removingSetting: false,
        }
    ),
    computed: {
        settings() {
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
        this.query = this.settingsQuery;
        this.page = this.query.current_page;
    },
    methods: {
        openAddSettingModal() {
            this.isOpenModal = true;
        },
        async removeSetting(id) {
            this.$buefy.dialog.confirm({
                message: 'Continue on this task?',
                onConfirm: async () => {
                    this.removingSetting = true;
                    try {
                        await $http.delete(`api/settings/${id}`);
                    } finally {
                        this.removingSetting = false;
                        await this.loadAsyncData();
                    }
                },
            });
        },
        async changeSetting(id, key, value) {
            this.savingSetting = true;
            try {
                await $http.patch(`api/settings/${id}`, {
                    key,
                    value,
                });
            } finally {
                this.savingSetting = false;
                await this.loadAsyncData();
            }
        },
        toggle(row) {
            this.$refs.settingsTable.toggleDetails(row);
        },

        changedPage(page) {
            this.page = page;
            this.loadAsyncData();
        },

        async loadAsyncData() {
            const params = this.getAllUrlParams(document.location.href)
            params.page = this.page;
            Object.assign(params, this.filters);
            this.loading = true;
            const request = await $http.get('/api/settings', {params: {...params}});
            this.query = await request.json();
            this.loading = false;
        },
    },
};
</script>
