<template>
  <div class="container mx-auto">
    <div class="w-full max-w-md mx-auto">
      <form
        method="POST"
        class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <label
          for="idn_type"
          class="label-base">Tipo de Identificación</label>
        <v-select
          id="idn_type"
          :class="{'border-red':hasInErrors('idn')}"
          :searchable="false"
          :clearable="false"
          :options="idnTypes"
          v-model="person.idn_type"
          name="idn_type"
          class="mb-3"
          @blur="cleanError('idn_type')"/>
        <span
          v-if="hasInErrors('idn_type')"
          class="error-base"
          role="alert">
          <strong>{{ getError('idn_type') }}</strong>
        </span>
        <label
          for="idn"
          class="label-base">Numero de Identificación</label>

        <input
          id="idn"
          :class="{'border-red':hasInErrors('idn')}"
          v-model="person.idn"
          type="text"
          class="input-base"
          name="idn"
          required
          autofocus
          @blur="cleanError('idn')">
        <span
          v-if="hasInErrors('idn')"
          class="error-base"
          role="alert">
          <strong>{{ getError('idn') }}</strong>
        </span>
        <label
          for="name"
          class="label-base">Nombre</label>

        <input
          id="name"
          :class="{'border-red':hasInErrors('name')}"
          v-model="person.name"
          type="text"
          class="input-base"
          name="name"
          required
          autofocus
          @blur="cleanError('name')">

        <span
          v-if="hasInErrors('name')"
          class="error-base"
          role="alert">
          <strong>{{ getError('name') }}</strong>
        </span>
        <label
          for="last_name"
          class="label-base">Apellido(s)</label>

        <input
          id="last_name"
          :class="{'border-red':hasInErrors('last_name')}"
          v-model="person.last_name"
          type="text"
          class="input-base"
          name="last_name"
          required
          autofocus
          @blur="cleanError('last_name')">

        <span
          v-if="hasInErrors('last_name')"
          class="error-base"
          role="alert">
          <strong>{{ getError('last_name') }}</strong>
        </span>

        <label
          for="phone"
          class="label-base">Telefono</label>

        <input
          id="phone"
          :class="{'border-red':hasInErrors('phone')}"
          v-model="person.phone"
          type="text"
          class="input-base"
          name="phone"
          required
          autofocus
          @blur="cleanError('phone')">

        <span
          v-if="hasInErrors('phone')"
          class="error-base"
          role="alert">
          <strong>{{ getError('phone') }}</strong>
        </span>

        <label
          class="label-base"
          for="email">Email</label>
        <input
          id="email"
          :class="{'border-red':hasInErrors('last_name')}"
          v-model="person.email"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
        <span
          v-if="hasInErrors('email')"
          class="error-base"
          role="alert">
          <strong>{{ getError('email') }}</strong>
        </span>

        <label
          class="label-base"
          for="email_confirmation">Confirmacion de Email</label>
        <input
          id="email_confirmation"
          :class="{'border-red':hasInErrors('email_confirmation')}"
          v-model="person.email_confirmation"
          class="input-base"
          type="email"
          name="email"
          required
          autofocus>
        <span
          v-if="hasInErrors('email_confirmation')"
          class="error-base"
          role="alert">
          <strong>{{ getError('email_confirmation') }}</strong>
        </span>
        <label
          for="address"
          class="label-base">Dirección</label>
        <textarea
          id="address"
          :class="{'border-red':hasInErrors('address')}"
          v-model="person.address"
          class="input-base"
          required
          autofocus
          name="address">Direccion</textarea>
        <span
          v-if="hasInErrors('address')"
          class="error-base"
          role="alert">
          <strong>{{ getError('address') }}</strong>
        </span>
        <button
          type="button"
          class="btn btn-primary mt-2"
          @click.prevent="registerPerson">
          Registrar
        </button>
      </form>
    </div>
  </div>
</template>
<script>
import Error from '../mixins/ErrorMixins';

export default {
  name: 'RegisterClient',
  mixins: [Error],
  props: {
    idn_type_imported: {
      type: String,
      default: '',
    },
    idn_imported: {
      type: String,
      default: '',
    },
    clientParent: {
      type: Number,
      default: null,
    },
  },
  data() {
    return {
      errors: [],
      person: {
        idn: '',
        idn_type: '',
        name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
      },
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT'],
      selectedRole: [],
    };
  },
  mounted() {
    if (this.user) {
      this.person = this.user;
    }
  },
  created() {
    if (this.idn_imported !== '') {
      this.person.idn = this.idn_imported;
    }
    if (this.idn_type_imported !== '') {
      this.person.idn_type = this.idn_type_imported;
    }
  },
  methods: {
    registerPerson() {
      if (this.clientParent !== null) {
        this.person.relatedSender = this.clientParent;
      }
      axios.post('/api/registerClient', this.person)
        .then(() => {
          $('#modal').modal('hide');
          this.$emit('registered');
        })
        .catch((error) => {
          if (this.errors) {
            this.errors = error.response.data.errors;
          }
        });
    },
  },
};
</script>

<style lang="css" scoped>
</style>
