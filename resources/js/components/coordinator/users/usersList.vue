<template>
  <div>
    <section class="section">
      <div class="columns">
        <div class="column">
          <button class="button field is-info"
                  @click="goToDetails"
                  :disabled="!selected.id">
            <span v-if="!selected.id">{{ $t('users.SELECT_USER') }}</span>
            <span
              v-else>{{$t('users.GO_TO_SELECTED')}} {{selected.name}} {{selected.last_name}}</span>
          </button>
        </div>
        <div class="column">
          <b-taginput
            v-model="selectedRoles"
            :data="filteredRoles"
            autocomplete
            :allow-new="false"
            :open-on-focus="true"
            field="name"
            icon="label"
            :placeholder="$t('users.SELECT:ROLE')"
            @typing="getFilteredTags">
          </b-taginput>
        </div>
      </div>

      <b-table
        :data="users"
        :loading="loading"
        @click="userClicked"
        paginated
        backend-pagination
        backend-filtering
        :striped="true"
        :total="query.total"
        :current-page="query.current_page"
        :per-page="query.per_page"
        @page-change="changedPage"
        @filters-change="changedFilter"
        aria-next-label="Next page"
        aria-previous-label="Previous page"
        :selected.sync="selected">

        <b-table-column field="id"
                        label="ID"
                        width="40"
                        numeric
                        sticky
                        v-slot="props">
          {{ props.row.id }}
        </b-table-column>

        <b-table-column field="name"
                        :label="$t('auth.NAME')"
                        searchable
                        v-slot="props">
          {{ props.row.name }}
        </b-table-column>

        <b-table-column
          field="last_name"
          :label="$t('auth.LAST_NAME')"
          searchable
          v-slot="props">
          {{ props.row.last_name }}
        </b-table-column>
        <b-table-column
          field="email"
          :label="$t('auth.EMAIL')"
          searchable
          v-slot="props">
          {{ props.row.email }}
        </b-table-column>
        <b-table-column
          field="idn_type"
          :label="$t('auth.IDN_TYPE')"
          searchable
          v-slot="props">
          {{ props.row.idn_type }}
        </b-table-column>
        <b-table-column
          field="idn"
          :label="$t('auth.IDN')"
          searchable
          v-slot="props">
          {{ props.row.idn }}
        </b-table-column>
        <b-table-column
          field="role"
          :label="$t('users.ROLE')"
          v-slot="props">
              <span class="tag"
                    v-for="role in props.row.roles"
                    :key="role.name_id"
                    :class="{
                    'is-danger':role.name_id==='coordinator',
                    'is-warning':role.name_id==='foreign_operator',
                    'is-success':role.name_id==='receiver',
                    'is-info':role.name_id==='venezuelan_operator',
                    'is-primary':role.name_id==='client'
                    }">
                 {{ role.name}}
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
  </div>
</template>
<script>
export default {
  name: 'UserList',
  props: {
    usersQuery: {
      type: Object,
      default: () => ({
        data: {},
      }),
    },
    allRoles: {
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
      selectedRoles: [],
      filteredRoles: [],
    }
  ),
  computed: {
    roles() {
      return this.allRoles.map((role) => role.name);
    },
    users() {
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
    selectedRoles() {
      this.loadAsyncData();
    },
  },
  created() {
    this.query = this.usersQuery;
    this.page = this.query.current_page;
    this.filteredRoles = this.allRoles;
  },
  methods: {
    userClicked(user) {
      if (user.id === this.selected.id) {
        this.selected = {};
      }
    },
    goToDetails() {
      window.location.href = `users/${this.selected.id}`;
    },
    changedPage(page) {
      this.page = page;
      this.loadAsyncData();
    },
    async loadAsyncData() {
      const params = this.getAllUrlParams(document.location.href);
      params.page = this.page;
      Object.assign(params, this.filters);
      if (this.selectedRoles.length) {
        params.roles = this.selectedRoles.map((role) => role.name_id);
      }
      this.loading = true;
      const request = await $http.get('/api/user', { params: { ...params } });
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
