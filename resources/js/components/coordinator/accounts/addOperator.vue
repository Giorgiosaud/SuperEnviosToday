<template>
  <div>
    <div
      class="modal-card"
      style="width: auto">
      <header class="modal-card-head">
        <p class="modal-card-title">
          Asocie un operador
        </p>
      </header>
      <section class="modal-card-body">
        <b-table
          :data="operatorsToSelect"
        >

          <b-table-column field="id" label="ID" numeric v-slot="{row:operator}">
            {{ operator.id }}
          </b-table-column>
          <b-table-column field="idn_type" label="tipo" v-slot="{row:operator}">
            {{ operator.idn_type }}
          </b-table-column>
          <b-table-column field="idn" label="Número" v-slot="{row:operator}">
            {{ operator.idn }}
          </b-table-column>
          <b-table-column field="name" label="Nombre" v-slot="{row:operator}">
            {{ operator.name }}
          </b-table-column>
          <b-table-column field="last_name" label="Apellido" v-slot="{row:operator}">
            {{ operator.last_name }}
          </b-table-column>
          <b-table-column field="email" label="Email" v-slot="{row:operator}">
            {{ operator.email }}
          </b-table-column>
          <b-table-column field="roles" label="Roles" v-slot="{row:operator}">
            <b-taglist>
              <b-tag v-for="(role,index) in showRoles(operator.roles)" :key="index" type="is-info">{{ role }}</b-tag>
            </b-taglist>
          </b-table-column>
          <b-table-column field="accion" label="Accion" v-slot="{row:operator}">
            <b-button type="is-success" @click="$emit('add-operator-to-account',{operator,account})">Agregar</b-button>
          </b-table-column>

        </b-table>
      </section>
      <footer class="modal-card-foot">
        <button
          class="button"
          type="button"
          @click="$parent.close()"
          @keypress.esc="$parent.close()">
          Cerrar
        </button>
      </footer>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddOperator',

  props: {
    account: {
      type: Object,
      default: () => ({}),
    },
    actualOwners: {
      type: Array,
      default: () => ([]),
    },
  },
  data: () => ({
    operators: [],
    columns: [{
      field: 'id',
      label: 'ID',
      width: '5',
      numeric: true,
    },
    {
      field: 'name',
      label: 'Nombre',
    }],

  }),
  computed: {
    operatorsToSelect() {
      return this.operators.filter((operator) => !this.actualOwners.includes(operator.id));
    },
  },
  created() {
    this.getOperators();
  },
  methods: {
    async getOperators() {
      const response = await $http.get('api/users/operators');
      this.operators = await response.json();
    },
    showRoles(roles) {
      return roles.map((role) => role.name);
    },
  },
};
</script>

<style scoped>

</style>
