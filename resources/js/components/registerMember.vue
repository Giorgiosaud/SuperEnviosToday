<template>
  <div class="container mx-auto">
    <div class="w-full max-w-md mx-auto">
      <label
        for="idn_type"
        class="label-base"
      >Tipo de Identificación</label>
      <v-select
        id="idn_type"
        v-model="person.idn_type"
        v-validate="'required'"
        :searchable="false"
        :clearable="false"
        :options="idnTypes"
        name="Tipo de Identificación"
        class="mb-3 bg-white"
      />
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Tipo de Identificación') }}</strong>
      </span>
      <label
        for="roles"
        class="label-base"
      >Rol</label>
      <v-select
        id="roles"
        v-model="person.roles"
        v-validate="'required'"
        :options="roles"
        class="bg-white"
        label="name"
        index="name_id"
        name="name_id"
        multiple
      />
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('role') }}</strong>
      </span>
      <label
        for="idn"
        class="label-base"
      >Numero de Identificación</label>

      <input
        id="idn"
        v-model="person.idn"
        v-validate="'required'"
        type="text"
        class="input-base"
        name="Número de Identificación"
      >
      <span

        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Número de Identificación') }}</strong>
      </span>
      <label
        for="name"
        class="label-base"
      >Nombre</label>

      <input
        id="name"
        v-model="person.name"
        v-validate="'required'"
        type="text"
        class="input-base"
        name="Nombre"
      >

      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Nombre') }}</strong>
      </span>
      <label
        for="last_name"
        class="label-base"
      >Apellido(s)</label>

      <input
        id="last_name"
        v-model="person.last_name"
        v-validate="'required'"
        type="text"
        class="input-base"
        name="Apellido"
      >

      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Apellido') }}</strong>
      </span>

      <label
        for="phone"
        class="label-base"
      >Telefono</label>

      <input
        id="phone"
        v-model="person.phone"
        v-validate="'required'"
        type="text"
        class="input-base"
        name="Teléfono"
      >

      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Teléfono') }}</strong>
      </span>

      <label
        class="label-base"
        for="email"
      >Email</label>
      <input
        id="email"
        ref="email"
        v-model="person.email"
        v-validate="'required|email'"
        class="input-base"
        type="email"
        name="Email"
      >
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Email') }}</strong>
      </span>

      <label
        class="label-base"
        for="email_confirmation"
      >Confirmacion de Email</label>
      <input
        id="email_confirmation"
        v-model="person.email_confirmation"
        v-validate="'required|email|confirmed:email'"
        class="input-base"
        type="email"
        name="Email Confirmation"
      >
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Email Confirmation') }}</strong>
      </span>
      <label
        for="address"
        class="label-base"
      >Dirección</label>

      <textarea
        id="address"
        v-model="person.address"
        v-validate="'required'"
        class="input-base"
        name="Dirección"
      />

      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Dirección') }}</strong>
      </span>
      <label
        for="password"
        class="label-base"
      >Clave</label>

      <input
        id="password"
        ref="password"
        v-model="person.password"
        v-validate="'required|min:8'"
        type="password"
        class="input-base"
        name="clave"
      >
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('clave') }}</strong>
      </span>

      <label
        for="password_confirmation"
        class="label-base"
      >Confirmación de Clave</label>

      <input
        id="password_confirmation"
        v-model="person.password_confirmation"
        v-validate="'required|confirmed:password'"
        type="password"
        class="input-base"
        name="Confirmación de Clave"
      >
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Confirmación de Clave') }}</strong>
      </span>
    </div>
    <div class="container">
      <div class="w-full max-w-md mx-auto">
        <button
          type="button"
          class="btn btn-primary mt-2"
          :disabled="errors.any()"
          @click.prevent="registerPerson"
        >
          Registrar
        </button>
      </div>
    </div>
  </div>
</template>
<script>

export default {
  name: 'RegisterMember',
  data() {
    return {
      person: {
        idn: '',
        idn_type: '',
        name: '',
        last_name: '',
        email: '',
        email_confirmation: '',
        password: '',
        password_confirmation: '',
        phone: '',
        address: '',
        roles: [],
      },
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT', 'RIF'],
      selectedRole: [],
      roles: [],
    };
  },
  created() {
    this.getFullRolesList();
  },
  methods: {
    getFullRolesList() {
      window.axios.get('/api/roles')
        .then((response) => {
          this.roles = response.data;
        });
    },
    registerPerson() {
      axios.post('/api/registerMember', this.person)
        .then((response) => {
          alert(response.data.message);
          this.person = {
            idn: '',
            idn_type: '',
            name: '',
            last_name: '',
            email: '',
            email_confirmation: '',
            password: '',
            password_confirmation: '',
            phone: '',
            address: '',
            roles: [],
          };
        });
    },
    hasRole(role) {
      return !!this.user.roles.filter(({ name }) => name === role).length;
    },
  },
};
</script>

<style lang="css" scoped>
</style>
