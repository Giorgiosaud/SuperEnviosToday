<template>
  <validation-observer
    tag="div"
    class="modal-card"
    style="width: auto">
    <header class="modal-card-head">
      <h2 class="modal-card-title">
        Nuevo Receptor
      </h2>
    </header>
    <div class="modal-card-body">
      <validation-provider
        v-slot="{ classes,errors,valid }"
        rules="required"
        name="idn_type"
        tag="div"
        class="control">
        <label class="label">Tipo de Documento</label>
        <div class="control has-icons-left has-icons-right">
          <div
            class="select"
            :class="classes">
            <select
              id="idn_type"
              v-model="idn_type"
              name="idn_type">
              <option value="">
                Seleccione el tipo de documento
              </option>
              <option value="CI">
                Cédula Venezolana
              </option>
              <option value="PASSPORT">
                Pasaporte
              </option>
              <option value="RUT">
                RUT
              </option>
              <option value="DNI">
                DNI
              </option>
              <option value="RIF">
                RIF
              </option>
            </select>
            <span
              v-if="valid"
              class="icon is-small has-text-success is-right">
              <font-awesome-icon icon="check" />
            </span>
          </div>
          <span class="icon is-small is-left">
            <font-awesome-icon icon="passport" />
          </span>
        </div>
        <strong
          v-if="errors[0]"
          class="help is-danger">{{ errors[0] }}</strong>
      </validation-provider>
      <validation-provider
        v-slot="{ classes,errors,valid}"
        rules="required"
        name="ID"
        tag="div"
        class="control">
        <label
          class="label"
          for="idn">ID</label>
        <div class="control has-icons-right">
          <input
            id="idn"
            v-model="idn"
            name="idn"
            class="input"
            :class="classes"
            type="text"
            placeholder="ID"
            value="123"
            autocomplete="idn">
          <span
            v-if="errors[0]"
            class="icon is-small has-text-warning is-right">
            <font-awesome-icon icon="exclamation-triangle" />
          </span>
          <span
            v-if="valid"
            class="icon is-small has-text-success is-right">
            <font-awesome-icon icon="check" />
          </span>
        </div>
        <strong
          v-if="errors[0]"
          class="help is-danger">{{ errors[0] }}</strong>
      </validation-provider>
      <validation-provider
        v-slot="{ classes,errors, valid }"
        rules="required"
        tag="div"
        class="field">
        <label
          class="label"
          for="name">Nombre</label>
        <div class="control has-icons-right">
          <input
            id="name"
            v-model="name"
            name="name"
            class="input"
            :class="classes"
            type="text"
            placeholder="Nombre"
            autocomplete="name">
          <span
            v-if="errors[0]"
            class="icon is-small has-text-warning is-right">
            <font-awesome-icon icon="exclamation-triangle" />
          </span>
          <span
            v-if="valid"
            class="icon is-small has-text-success is-right">
            <font-awesome-icon icon="check" />
          </span>
        </div>
        <strong
          v-if="errors[0]"
          class="help is-danger">{{ errors[0] }}</strong>
      </validation-provider>
      <validation-provider
        v-slot="{ classes,errors, valid }"
        rules="required"
        tag="div"
        class="field">
        <label
          class="label"
          for="last_name">Apellido</label>
        <div class="control has-icons-right">
          <input
            id="last_name"
            v-model="last_name"
            name="name"
            class="input"
            :class="classes"
            type="text"
            placeholder="Apellido"
            autocomplete="name">
          <span
            v-if="errors[0]"
            class="icon is-small has-text-warning is-right">
            <font-awesome-icon icon="exclamation-triangle" />
          </span>
          <span
            v-if="valid"
            class="icon is-small has-text-success is-right">
            <font-awesome-icon icon="check" />
          </span>
        </div>
        <strong
          v-if="errors[0]"
          class="help is-danger">{{ errors[0] }}</strong>
      </validation-provider>
      <validation-provider
        tag="div"
        class="column is-narrow"
        vid="sameEmail">
        <label class="checkbox">
          <input
            v-model="sameEmail"
            type="checkbox">
          Usar Mismo Correo que Cliente
        </label>
      </validation-provider>
      <validation-provider
        v-if="!sameEmail"
        v-slot="{ classes,errors,valid }"
        name="email"
        rules="required|email"
        tag="div"
        class="field">
        <label
          class="label"
          for="email">Email</label>
        <div class="control has-icons-left has-icons-right">
          <input
            id="email"
            v-model="email"
            name="email"
            :class="classes"
            class="input"
            type="text"
            placeholder="email"
            autocomplete="email">
          <span class="icon is-small is-left">
            <font-awesome-icon icon="envelope" />
          </span>
          <span
            v-if="errors[0]"
            class="icon is-small has-text-warning is-right">
            <font-awesome-icon icon="exclamation-triangle" />
          </span>
          <span
            v-if="valid"
            class="icon is-small has-text-success is-right">
            <font-awesome-icon icon="check" />
          </span>
        </div>
        <strong
          v-if="errors[0]"
          class="help is-danger">@{{ errors[0] }}</strong>
      </validation-provider>
    </div>
    <footer class="modal-card-foot">
      <b-button
        :loading="savingData"
        class="button is-primary"
        @click="saveReceiver">
        Guardar
      </b-button>
      <button
        class="button"
        type="button"
        @click="cleanAndClose"
        @keyup.esc="cleanAndClose">
        Cancelar
      </button>
    </footer>
  </validation-observer>
</template>
<script>
export default {
  name: 'NewReceiverForm',
  props: {
    client: {
      type: [Object, null],
      default: () => null,
    },
  },
  data: () => ({
    idn: '',
    idn_type: '',
    name: '',
    last_name: '',
    sameEmail: true,
    email: '',
    savingData: false,
  }),
  watch: {
    sameEmail(value) {
      if (value) {
        this.email = this.client.email;
      } else {
        this.email = '';
      }
    },
  },
  created() {
    this.email = this.client.email;
  },
  methods: {
    cleanData() {
      this.idn = '';
      this.idn_type = '';
      this.name = '';
      this.last_name = '';
      this.sameEmail = true;
      this.email = this.client.email;
    },
    async saveReceiver() {
      this.savingData = true;
      try {
        const response = await $http.post(`/api/user/${this.client.id}/receiver`, {
          idn: this.idn,
          idn_type: this.idn_type,
          name: this.name,
          last_name: this.last_name,
          email: this.email,
        });
        this.$emit('receiver-added', response);
        this.cleanAndClose();
      } catch (error) {
        this.$buefy.notification.open({
          message: error.message,
          type: 'is-danger',
        });
      } finally {
        this.savingData = false;
      }
    },
    cleanAndClose() {
      this.cleanData();
      this.$parent.close();
    },
  },

};
</script>
