<template>
  <div>
    <div class="col-12">
      <label for="bankId">Seleccione el Banco</label>
      <v-select
        id="bankId"
        v-model="account_bank_id"
        v-validate="'required'"
        :options="banks"
        index="id"
        name="Banco"
        label="name"
      />
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Banco') }}</strong>
      </span>
    </div>
    <div class="col-12">
      <label for="account_type">Seleccione el Tipo de Cuenta</label>
      <v-select
        id="account_type"
        v-model="account_type"
        v-validate="'required'"
        :options="account_types"
        index="index"
        name="Tipo de Cuenta"
      />
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Tipo de Cuenta') }}</strong>
      </span>
    </div>
    <div class="col-12">
      <label for="number">Número de Cuenta</label>
      <input
        id="number"
        v-model="number"
        v-validate="'required|length:20'"
        name="Numero de cuenta"
        type="text"
        class="input-base"
      >
      <span
        class="error-base"
        role="alert"
      >
        <strong>{{ errors.first('Numero de cuenta') }}</strong>
      </span>
    </div>
    <div class="col-12">
      <button
        type="button"
        class="btn btn-primary mt-2"
        :disabled="errors.any()"
        @click.prevent="addAccount"
      >
        Registrar Cuenta
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'AddVenezuelanAccount',
  props: {
    client: {
      type: Object,
      default() {
        return {};
      },
    },
  },
  data() {
    return {
      account_bank_id: '',
      currency_id: '',
      currencies: [],
      banks: [],
      number: '',
      account_type: '',
      account_types: [
        {
          label: 'Corriente',
          index: 'corriente',
        },
        {
          label: 'Ahorro',
          index: 'ahorro',
        },
      ],
    };
  },
  created() {
    axios.get('api/venezuelan_banks').then((response) => {
      this.banks = response.data;
    });
    axios.get('api/currencies').then((response) => {
      this.currencies = response.data;
    });
  },
  methods: {
    addAccount() {
      axios.post('api/accounts', {
        user_id: this.client.id,
        bank_id: this.account_bank_id,
        type: this.account_type,
        number: this.number,
        is_operator_account: false,
      })
        .then(() => {
          this.$emit('accountRegistered');
          this.account_bank_id = '';
          this.account_type = '';
          this.number = '';
        });
    },
  },


};
</script>

<style scoped>
</style>
