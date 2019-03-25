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
        :class="{'border-red':hasInErrors('idn')}"
        :searchable="false"
        :clearable="false"
        :options="idnTypes"
        name="idn_type"
        class="mb-3 bg-white"
        @blur="cleanError('idn_type')"
      />
      <span
        v-if="hasInErrors('idn_type')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('idn_type') }}</strong>
      </span>
      <label
        for="roles"
        class="label-base"
      >Rol</label>
      <v-select
        id="roles"
        v-model="person.roles"
        :options="roles"
        class="bg-white"
        label="name"
        index="name_id"
        multiple
      />
      <!--v-select label="name" index="name_id" multiple id="roles" name="roles" class="mb-3"
                      :class="{'border-red':hasInErrors('role')}" :searchable="false" :clearable="false"
                      :options='roles' v-model="person.roles" @blur="cleanError('role')"></v-select-->
      <span
        v-if="hasInErrors('role')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('role') }}</strong>
      </span>
      <label
        for="idn"
        class="label-base"
      >Numero de Identificación</label>

      <input
        id="idn"
        v-model="person.idn"
        :class="{'border-red':hasInErrors('idn')}"
        type="text"
        class="input-base"
        name="idn"
        required
        autofocus
        @blur="cleanError('idn')"
      >
      <span
        v-if="hasInErrors('idn')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('idn') }}</strong>
      </span>
      <label
        for="name"
        class="label-base"
      >Nombre</label>

      <input
        id="name"
        v-model="person.name"
        :class="{'border-red':hasInErrors('name')}"
        type="text"
        class="input-base"
        name="name"
        required
        autofocus
        @blur="cleanError('name')"
      >

      <span
        v-if="hasInErrors('name')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('name') }}</strong>
      </span>
      <label
        for="last_name"
        class="label-base"
      >Apellido(s)</label>

      <input
        id="last_name"
        v-model="person.last_name"
        :class="{'border-red':hasInErrors('last_name')}"
        type="text"
        class="input-base"
        name="last_name"
        required
        autofocus
        @blur="cleanError('last_name')"
      >

      <span
        v-if="hasInErrors('last_name')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('last_name') }}</strong>
      </span>

      <label
        for="phone"
        class="label-base"
      >Telefono</label>

      <input
        id="phone"
        v-model="person.phone"
        :class="{'border-red':hasInErrors('phone')}"
        type="text"
        class="input-base"
        name="phone"
        required
        autofocus
        @blur="cleanError('phone')"
      >

      <span
        v-if="hasInErrors('phone')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('phone') }}</strong>
      </span>

      <label
        class="label-base"
        for="email"
      >Email</label>
      <input
        id="email"
        v-model="person.email"
        :class="{'border-red':hasInErrors('last_name')}"
        class="input-base"
        type="email"
        name="email"
        required
        autofocus
      >
      <span
        v-if="hasInErrors('email')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('email') }}</strong>
      </span>

      <label
        class="label-base"
        for="email_confirmation"
      >Confirmacion de Email</label>
      <input
        id="email_confirmation"
        v-model="person.email_confirmation"
        :class="{'border-red':hasInErrors('email_confirmation')}"
        class="input-base"
        type="email"
        name="email"
        required
        autofocus
      >
      <span
        v-if="hasInErrors('email_confirmation')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('email_confirmation') }}</strong>
      </span>
      <label
        for="address"
        class="label-base"
      >Dirección</label>

      <textarea
        id="address"
        v-model="person.address"
        :class="{'border-red':hasInErrors('address')}"
        class="input-base"
        required
        autofocus
        name="address"
      />

      <span
        v-if="hasInErrors('address')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('address') }}</strong>
      </span>
      <label
        for="password"
        class="label-base"
      >Clave</label>

      <input
        id="password"
        v-model="person.password"
        :class="{'border-red':hasInErrors('password')}"
        type="password"
        class="input-base"
        name="password"
        required
      >
      <span
        v-if="hasInErrors('password')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('password') }}</strong>
      </span>

      <label
        for="password_confirmation"
        class="label-base"
      >Confirmación de Clave</label>

      <input
        id="password_confirmation"
        v-model="person.password_confirmation"
        :class="{'border-red':hasInErrors('password_confirmation')}"
        type="password"
        class="input-base"
        name="password_confirmation"
        required
      >
      <span
        v-if="hasInErrors('password_confirmation')"
        class="error-base"
        role="alert"
      >
        <strong>{{ getError('password_confirmation') }}</strong>
      </span>

      <button
        type="button"
        class="btn btn-primary mt-2"
        @click.prevent="registerPerson"
      >
        Registrar
      </button>
    </div>
  </div>
</template>
<script>
import Error from '../mixins/ErrorMixins';

export default {
  name: 'RegisterMember',
  mixins: [Error],
  data() {
    return {
      errors: [],
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
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT','RIF'],
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
          alert(response);
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
          this.errors = [];
        })
        .catch((error) => {
          this.errors = error.response.data.errors;
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
