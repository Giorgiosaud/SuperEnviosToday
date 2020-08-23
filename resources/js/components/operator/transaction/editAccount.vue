<template>
  <validation-observer
    v-slot="{invalid}"
    tag="div"
    class="modal-card"
    style="width: auto">
    <header class="modal-card-head">
      <h2 class="modal-card-title">
        Nueva Cuenta
      </h2>
    </header>
    <div class="modal-card-body">
      <validation-provider
        v-slot="{ classes,errors,valid }"
        rules="required"
        name="bank"
        tag="div"
        class="control">
        <label class="label">Banco</label>
        <div class="control has-icons-left has-icons-right">
          <div
            class="select"
            :class="classes">
            <select
              id="bank"
              v-model="selectedBank"
              name="bank">
              <option value="">
                Seleccione el Banco
              </option>
              <option
                v-for="bank in venezuelanBanks"
                :key="bank.id"
                :value="bank">
                {{ bank.name }}
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
        v-slot="{ classes,errors,valid }"
        rules="required"
        name="type"
        tag="div"
        class="control">
        <label class="label">Tipo de Cuenta</label>
        <div class="control has-icons-left has-icons-right">
          <div
            class="select"
            :class="classes">
            <select
              id="type"
              v-model="selectedType"
              name="type">
              <option value="">
                Seleccione el Tipo de cuenta
              </option>
              <option
                v-for="type in types"
                :key="type.id"
                :value="type.value">
                {{ type.name }}
              </option>
            </select>
            <span
              v-if="valid"
              class="icon is-small has-text-success is-right">
              <font-awesome-icon icon="check" />
            </span>
          </div>
          <span class="icon is-small is-left">
            <font-awesome-icon icon="piggy-bank" />
          </span>
        </div>
        <strong
          v-if="errors[0]"
          class="help is-danger">{{ errors[0] }}</strong>
      </validation-provider>
      <validation-provider
        v-slot="{ classes,errors,valid}"
        rules="required|length:20"
        name="Numero de cuenta"
        tag="div"
        class="control">
        <label
          class="label"
          for="account-number">Numero de cuenta</label>
        <div class="control has-icons-right">
          <the-mask
            id="account-number"
            v-model="accountNumber"
            name="accountNumber"
            class="input"
            :class="classes"
            type="text"
            mask="####-####-####-####-####"
            placeholder="Número de Cuenta" />
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
    </div>
    <footer class="modal-card-foot">
      <b-button
        :loading="updatingData"
        class="button is-primary"
        :disabled="invalid"
        @click="updateAccount">
        Guardar
      </b-button>
      <button
        class="button"
        type="button"
        @click="close"
        @keyup.esc="close">
        Cancelar
      </button>
    </footer>
  </validation-observer>
</template>
<script>
export default {
  name: 'EditAccount',
  props: {
    receiver: {
      type: [Object, null],
      default: () => null,
    },
    account: {
      type: [Object, null],
      default: () => null,
    },
  },
  data: () => ({
    venezuelanBanks: [],
    id: '',
    types: [{ name: 'Corriente', value: 'corriente' }, { name: 'Ahorro', value: 'ahorro' }],
    selectedBank: '',
    selectedType: '',
    updatingData: false,
    accountNumber: '',
  }),
  watch: {
    account: {
      immediate: true,
      handler(value) {
        if (value) {
          this.id = value.id;
          this.selectedBank = value.bank;
          this.selectedType = value.type;
          this.accountNumber = value.number;
        } else {
          this.id = '';
          this.selectedBank = '';
          this.selectedType = '';
          this.accountNumber = '';
        }
      },
    },
  },
  async created() {
    const response = await $http.get('/api/banks/base/');
    const banksBase = await response.json();
    this.venezuelanBanks = banksBase.sort((bank) => bank.name);
  },
  methods: {
    async updateAccount() {
      this.updatingData = true;
      try {
        const response = await $http.patch(`/api/account/${this.id}`, {
          bank_id: this.selectedBank.id,
          type: this.selectedType,
          number: this.accountNumber,
        });
        this.$emit('account-edited', response);
        this.close();
      } catch (error) {
        this.$buefy.notification.open({
          message: error.message,
          type: 'is-danger',
        });
      } finally {
        this.updatingData = false;
      }
    },
    cleanData() {
      this.selectedBank = '';
      this.selectedType = '';
      this.accountNumber = '';
    },
    close() {
      this.$parent.close();
    },
  },

};
</script>
