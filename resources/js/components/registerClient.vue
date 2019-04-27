<template>
  <div class="container mx-auto">
    <div class="w-100">
      <form
        method="POST"
        class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4"
      >
        <div class="col-12">
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
            class="mb-3"
            name="Tipo de Identificación"
          />
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Tipo de Identificación') }}</strong>
          </span>
        </div>
        <div class="col-12">
          <label
            for="idn"
            class="label-base"
          >Número de Identificación</label>
          <input
            id="idn"
            v-model="person.idn"
            v-validate="'required'"
            type="text"
            class="input-base"
            name="Numero de Identificación"
            @blur="getClientDataIfExist"
          >
          <span
            class=" error-base"
            role="alert"
          >
            <strong>{{ errors.first('Numero de Identificación') }}</strong>
          </span>
        </div>
        <div class="col-12">
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
            required
            autofocus
          >
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Nombre') }}</strong>
          </span>
        </div>
        <div class="col-12">
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
            required
            autofocus
          >
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Apellido') }}</strong>
          </span>
        </div>
        <div class="col-12">
          <label
            for="phone"
            class="label-base"
          >Telefono</label>
          <input
            id="phone"
            v-model="person.phone"
            v-validate="'required|min:9'"
            type="text"
            class="input-base"
            name="Telefono"
          >
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Telefono') }}</strong>
          </span>
        </div>
        <div class="col-12">
          <label
            class="label-base"
            for="email"
          >Email</label>
          <input
              autocomplete="false"
              class="input-base"
              id="email"
              name="Email"
              ref="email"
              type="email"
              v-model="person.email"
              v-validate="'required|email'"
          >
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Email') }}</strong>
          </span>
        </div>
        <div class="col-12">
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
            name="Confirmacion de Email"
            required
          >
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Confirmacion de Email') }}</strong>
          </span>
        </div>
        <div class="col-12">
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
          >
                    Dirección
                </textarea>
          <span
            class="error-base"
            role="alert"
          >
            <strong>{{ errors.first('Dirección') }}</strong>
          </span>
        </div>
        <div class="col-12">
          <button
            type="button"
            class="btn btn-primary mt-2"
            :disabled="errors.any()"
            @click.prevent="registerPerson"
            v-text="buttonText"
          />
        </div>
      </form>
    </div>
  </div>
</template>
<script>
export default {
  name: 'RegisterClient',
  props: {
    idnTypeImported: {
      type: String,
      default: '',
    },
    idnImported: {
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
      person: {
        idn: '',
        idn_type: '',
        name: '',
        last_name: '',
        email: '',
        phone: '',
        address: '',
      },
      idnTypes: ['CI', 'DNI', 'RUT', 'PASSPORT', 'RIF'],
      selectedRole: [],
    };
  },
  computed: {
    buttonText() {
      if (this.clientParent) {
        return 'Asociar Receptor';
      }
      return 'Registrar Cliente';
    },
  },
  mounted() {
    if (this.user) {
      this.person = this.user;
    }
  },
  created() {
    if (this.idnImported !== '') {
      this.person.idn = this.idnImported;
    }
    if (this.idnTypeImported !== '') {
      this.person.idn_type = this.idnTypeImported;
    }
  },
  methods: {
    getClientDataIfExist() {
      if (this.person.idn !== '' && this.person.idn_type !== '') {
        axios.get('api/user_data', {
          params: {
            idn_type: this.person.idn_type, idn: this.person.idn,
          },
        })
          .then(({ data }) => {
            if (data.length) {
              [this.person] = data;
            }
          });
      }
    },
    registerPerson() {
      this.$validator.validate().then((valid) => {
        if (valid) {
          if (this.clientParent) {
            this.person.relatedSender = this.clientParent;
          }
          axios.post('/api/registerClient', this.person)
            .then(() => {
              this.person.idn = '';
              this.person.idn_type = '';
              this.person.name = '';
              this.person.last_name = '';
              this.person.email = '';
              this.person.phone = '';
              this.person.address = '';
              $('#modal').modal('hide');
              this.$emit('registered');
            });
        }
      });
    },
  },
};
</script>
