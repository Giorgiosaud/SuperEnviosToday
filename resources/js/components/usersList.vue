<template>
  <div>
    <div class="container">
      <div class="row">
        <div class="col-12 col-md-3">
          <h1>Usuarios</h1>
        </div>
        <div class="col-12 col-md-9">
          <label
            for="query"
            class="label-base">Consulta</label> <input
              id="query"
              v-model="query"
              class="input-base">
        </div>
      </div>
    </div>
    <div class="container">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th
                v-for="header in headers"
                class="text-left">{{ header }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users">

              <td v-for="key in keysToShow">
                <span v-if="key==='idn'">
                  {{ user["idn_type"] }}-{{ user[key] }}
                </span>
                <span v-else>
                  {{ user[key] }}
                </span>
              </td>
              <td>
                <span
                  class="cursor-pointer"
                  data-toggle="modal"
                  data-target="#user-modal"
                  @click="editUser(user)">Editar</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <div
      id="paginator"
      class="container">
      <div class="notifications-paginator py-3">
        <nav
          v-if="last_page>1"
          aria-label="Page navigation example">
          <ul class="pagination align-items-center justify-content-center">
            <li
              v-if="current_page>1"
              class="page-item">
              <a
                class="page-link"
                @click.prevent="goPrev">
                <i class="material-icons">chevron_left</i>
              </a>
            </li>
            <li
              v-for="page in paginationArray"
              :key="page"
              :class="{active:page===current_page}"
              class="page-item">
              <a
                class="page-link"
                @click.prevent="gotoUsersPage(page)">{{ page }}</a>
            </li>
            <li
              v-if="current_page<last_page"
              class="page-item">
              <a
                class="page-link"
                @click.prevent="goNext">
              <i class="material-icons">chevron_right</i></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
    <div
      id="user-modal"
      class="modal "
      tabindex="-1"
      role="dialog">
      <div
        class="modal-dialog modal-xl"
        role="document">
        <div
          v-if="selectedUser"
          class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ selectedUser.name }} {{ selectedUser.last_name }}</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <label
              for="idn_type"
              class="label-base">Tipo de Identificación</label>
            <v-select
              id="idn_type"
              :searchable="false"
              :clearable="false"
              :options="idnTypes"
              v-model="selectedUser.idn_type"
              index="name_id"
              name="idn_type"
              class="mb-3"
            />
            <div class="form-group">
              <label
                for="roles"
                class="label-base">Roles</label>
              <select
                id="roles"
                v-model="selectedUser.roles"
                class="mb-3 form-control"
                label="name"
                multiple>
                <option
                  v-for="role in roles"
                  :value="role.name_id">{{ role.name }}</option>
              </select>
            </div>
            <label
              for="idn"
              class="label-base">Numero de Identificación</label>
            <input
              id="idn"
              v-model="selectedUser.idn"
              type="text"
              class="input-base"
              name="idn"
              required
              autofocus>
            <label
              for="name"
              class="label-base">Nombre</label>
            <input
              id="name"
              v-model="selectedUser.name"
              type="text"
              class="input-base"
              name="name"
              required
              autofocus>
            <label
              for="last_name"
              class="label-base">Apellido(s)</label>
            <input
              id="last_name"
              v-model="selectedUser.last_name"
              type="text"
              class="input-base"
              name="last_name"
              required
              autofocus>
            <label
              for="phone"
              class="label-base">Telefono</label>
            <input
              id="phone"
              v-model="selectedUser.phone"
              type="text"
              class="input-base"
              name="phone"
              required
              autofocus>
            <label
              class="label-base"
              for="email">Email</label>
            <input
              id="email"
              v-model="selectedUser.email"
              class="input-base"
              type="email"
              name="email"
              required
              autofocus>
            <label
              for="address"
              class="label-base">Dirección</label>

            <textarea
              id="address"
              v-model="selectedUser.address"
              class="input-base"
              required
              autofocus
              name="address"/>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal">Close</button>
            <button
              type="button"
              class="btn btn-primary"
              @click="guardarUsuario">Guardar Cambios</button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'UsersList',
  data() {
    return {
      users: [],
      roles: [],
      headers: [
        'Identificacion', 'Nombre', 'Apellido', 'E-Mail', 'Teléfono', 'Direccion',
      ],
      keysToShow: [
        'idn', 'name', 'last_name', 'email', 'phone', 'address',
      ],
      maxPagination: 4,
      query: '',
      last_page: '',
      selectedUser: null,
      current_page: '',
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT'],

    };
  },
  created() {
    this.getUsers();
    this.getFullRolesList();
  },
  watch: {
    query: _.debounce(function () {
      console.log(this);
      if (this.query.length >= 2) {
        window.axios.get('/api/users', {
          params: {
            q: this.query,
          },

        }).then((response) => {
          this.setData(response.data);
        });
      } else {
        window.axios.get('/api/users').then((response) => {
          this.setData(response.data);
        });
      }
    }, 400),
  },
  updated() {
    const instance = new Mark(document.querySelector('table.table'));
    instance.unmark();
    instance.mark(this.query, {
      element: 'span',
      className: 'highlight',
    });
  },
  methods: {
    setData(data) {
      this.users = data.data;
      this.last_page = data.last_page;
      this.current_page = data.current_page;
      this.response = data;
    },
    gotoUsersPage(page) {
      window.axios.get('/api/users', { params: { page, q: this.query } }).then((response) => {
        this.setData(response.data);
      });
    },
    goPrev() {
      this.gotoUsersPage(parseInt(this.current_page, 10) - 1);
    },
    goNext() {
      this.gotoUsersPage(parseInt(this.current_page, 10) + 1);
    },
    getUsers() {
      window.axios.get('/api/users').then((response) => {
        this.setData(response.data);
      });
    },
    getFullRolesList() {
      window.axios.get('/api/roles').then((response) => {
        this.roles = response.data;
        /* .map(role => {
                    return {value: role.name_id, label: role.name}
                }); */
      });
    },
    editUser(user) {
      this.selectedUser = user;
      this.selectedUser.roles = this.selectedUser.roles.map(role => role.name_id);
    },
    guardarUsuario() {
      window.axios.patch(`api/user/${this.selectedUser.id}`, this.selectedUser)
        .then((response) => {
          console.log(response);
        });
    },
  },
  computed: {
    paginationArray() {
      const halfPlusOne = Math.floor(this.maxPagination / 2) + 1;
      if (this.last_page < this.maxPagination) {
        return Array(this.last_page).fill().map((x, i) => i + 1);
      } else if (this.current_page < halfPlusOne) {
        return Array(this.maxPagination).fill().map((x, i) => i + 1);
      } else if (this.last_page - this.current_page < halfPlusOne) {
        return Array(this.maxPagination).fill()
          .map((x, i) => i + (this.last_page - (this.maxPagination - 1)));
      }
      return Array(this.maxPagination).fill()
        .map((x, i) => i + (this.current_page - Math.floor(this.maxPagination / 2)));
    },
  },
};
</script>

<style scoped>
    .material-icons {
        font-size: 12px;
    }

</style>
